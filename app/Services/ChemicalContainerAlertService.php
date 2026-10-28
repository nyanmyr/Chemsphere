<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\Alert;
use App\Models\Chemical;
use App\Models\ChemicalContainer;
use App\Models\User;
use Illuminate\Support\Collection;

class ChemicalContainerAlertService
{
    public function __construct(protected int $expiringSoonMonths = 3, protected float $lowStockThreshold = 0.2) {}

    public function expirationStatus(ChemicalContainer $container): ?AlertType
    {
        if (!$container->expiration_date) return null;
        if ($container->expiration_date->lte(today())) return AlertType::EXPIRED;
        if ($container->expiration_date->lte(today()->addMonthsNoOverflow($this->expiringSoonMonths))) return AlertType::EXPIRING_SOON;
        return null;
    }

    public function chemicalStatuses(Chemical $chemical): array
    {
        $statuses = [];
        foreach ($chemical->containers as $container) {
            if ($status = $this->expirationStatus($container)) $statuses[] = $status;
            if ($container->current_quantity <= 0) $statuses[] = AlertType::OUT_OF_STOCK;
            elseif ($container->current_quantity <= $container->quantity_received * $this->lowStockThreshold) $statuses[] = AlertType::LOW_STOCK;
        }
        return array_values(array_unique($statuses, SORT_REGULAR));
    }

    public function checkContainer(ChemicalContainer $container): void
    {
        $container->loadMissing('chemical');
        $name = $container->chemical?->chemical_name ?? 'Chemical';
        $status = $this->expirationStatus($container);
        if ($status) {
            $date = $container->expiration_date->toFormattedDateString();
            $this->notify($container, $status, $status === AlertType::EXPIRED
                ? "{$name} (batch {$container->batch_number}) expired on {$date}."
                : "{$name} (batch {$container->batch_number}) expires on {$date}.");
        } else {
            $this->resolve($container, [AlertType::EXPIRED, AlertType::EXPIRING_SOON]);
        }

        if ($container->current_quantity <= 0) {
            $this->notify($container, AlertType::OUT_OF_STOCK, "{$name} (batch {$container->batch_number}) is out of stock.");
            $this->resolve($container, [AlertType::LOW_STOCK]);
        } elseif ($container->current_quantity <= $container->quantity_received * $this->lowStockThreshold) {
            $this->notify($container, AlertType::LOW_STOCK, "{$name} (batch {$container->batch_number}) is low: {$container->current_quantity} containers remaining.");
            $this->resolve($container, [AlertType::OUT_OF_STOCK]);
        } else {
            $this->resolve($container, [AlertType::LOW_STOCK, AlertType::OUT_OF_STOCK]);
        }
    }

    private function notify(ChemicalContainer $container, AlertType $type, string $message): void
    {
        foreach (User::receivesAlerts()->get() as $recipient) {
            $existing = $recipient->alerts()->where('chemical_container_id', $container->chemical_container_id)
                ->where('alert_type', $type->value)->unread()->first();
            if ($existing) $existing->update(['message' => $message]);
            else $recipient->alerts()->create([
                'item_id' => $container->chemical_id,
                'chemical_container_id' => $container->chemical_container_id,
                'alert_type' => $type->value,
                'message' => $message,
            ]);
        }
    }

    private function resolve(ChemicalContainer $container, array $types): void
    {
        Alert::query()->where('chemical_container_id', $container->chemical_container_id)
            ->whereIn('alert_type', array_map(fn ($type) => $type->value, $types))
            ->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function lowStockThreshold(): float { return $this->lowStockThreshold; }
}
