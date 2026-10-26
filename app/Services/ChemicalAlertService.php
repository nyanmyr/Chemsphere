<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\Chemical;
use App\Models\User;
use Illuminate\Support\Collection;

class ChemicalAlertService
{
    public function __construct(
        protected int $expiringSoonMonths = 3,
        protected float $lowStockThreshold = 0.2, // 20% of quantity_received
    ) {}

    public function checkChemical(Chemical $chemical): void
    {
        $this->checkExpiration($chemical);
        $this->checkStockLevel($chemical);
    }

    public function expirationStatus(Chemical $chemical): ?AlertType
    {
        if (!$chemical->expiration_date) {
            return null;
        }

        $expiresAt = now()->parse($chemical->expiration_date);

        if ($expiresAt->lte(today())) {
            return AlertType::EXPIRED;
        }

        if ($expiresAt->lte(today()->addMonthsNoOverflow($this->expiringSoonMonths))) {
            return AlertType::EXPIRING_SOON;
        }

        return null;
    }

    protected function checkExpiration(Chemical $chemical): void
    {
        $status = $this->expirationStatus($chemical);

        if ($status === null) {
            return;
        }

        $expiresOn = now()->parse($chemical->expiration_date)->toFormattedDateString();

        $this->notify(
            $chemical,
            $status,
            $status === AlertType::EXPIRED
                ? "{$chemical->chemical_name} (batch {$chemical->batch_number}) expired on {$expiresOn}."
                : "{$chemical->chemical_name} (batch {$chemical->batch_number}) expires on {$expiresOn}."
        );
    }

    public function expiringSoonMonths(): int
    {
        return $this->expiringSoonMonths;
    }

    protected function checkStockLevel(Chemical $chemical): void
    {
        if ($chemical->current_quantity <= 0) {
            $this->notify(
                $chemical,
                AlertType::OUT_OF_STOCK,
                "{$chemical->chemical_name} (batch {$chemical->batch_number}) is out of stock."
            );
            return;
        }

        $threshold = $chemical->quantity_received * $this->lowStockThreshold;

        if ($chemical->current_quantity <= $threshold) {
            $this->notify(
                $chemical,
                AlertType::LOW_STOCK,
                "{$chemical->chemical_name} (batch {$chemical->batch_number}) is low: {$chemical->current_quantity} containers remaining."
            );
        }
    }

    protected function notify(Chemical $chemical, AlertType $type, string $message): void
    {
        foreach ($this->recipientsFor($chemical) as $recipient) {
            $existing = $recipient->alerts()
                ->where('item_id', $chemical->chemical_id)
                ->where('alert_type', $type->value)
                ->unread()
                ->first();

            if ($existing) {
                $existing->update(['message' => $message]);
                continue;
            }

            $recipient->alerts()->create([
                'item_id' => $chemical->chemical_id,
                'alert_type' => $type,
                'message' => $message,
            ]);
        }
    }

    protected function recipientsFor(Chemical $chemical): Collection
    {
        return User::receivesAlerts()->get();
    }

    public function lowStockThreshold(): float
    {
        return $this->lowStockThreshold;
    }
}
