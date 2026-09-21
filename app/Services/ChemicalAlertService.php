<?php

namespace App\Services;

use App\AlertType;
use App\Models\Chemical;
use App\Models\User;
use Illuminate\Support\Collection;

class ChemicalAlertService
{
    public function __construct(
        protected int $expiringSoonDays = 30,
        protected float $lowStockThreshold = 0.2, // 20% of initial_quantity
    ) {}

    public function checkChemical(Chemical $chemical): void
    {
        $this->checkExpiration($chemical);
        $this->checkStockLevel($chemical);
    }

    protected function checkExpiration(Chemical $chemical): void
    {
        $expiresAt = $chemical->expiration_date;

        if (!$expiresAt) {
            return;
        }

        $expiresAt = now()->parse($expiresAt);

        if ($expiresAt->isPast()) {
            $this->notify(
                $chemical,
                AlertType::EXPIRED,
                "{$chemical->chemical_name} (batch {$chemical->batch_number}) expired on {$expiresAt->toFormattedDateString()}."
            );
            return;
        }

        if (now()->diffInDays($expiresAt, false) <= $this->expiringSoonDays) {
            $this->notify(
                $chemical,
                AlertType::EXPIRING_SOON,
                "{$chemical->chemical_name} (batch {$chemical->batch_number}) expires on {$expiresAt->toFormattedDateString()}."
            );
        }
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

        $threshold = $chemical->initial_quantity * $this->lowStockThreshold;

        if ($chemical->current_quantity <= $threshold) {
            $this->notify(
                $chemical,
                AlertType::LOW_STOCK,
                "{$chemical->chemical_name} (batch {$chemical->batch_number}) is low: {$chemical->current_quantity} {$chemical->unit} remaining."
            );
        }
    }

    protected function notify(Chemical $chemical, AlertType $type, string $message): void
    {
        foreach ($this->recipientsFor($chemical) as $recipient) {
            $existing = $recipient->alerts()
                ->where('chemical_id', $chemical->chemical_id)
                ->where('alert_type', $type->value)
                ->unread()
                ->first();

            if ($existing) {
                $existing->update(['message' => $message]); // refresh e.g. new quantity
                continue;
            }

            $recipient->alerts()->create([
                'chemical_id' => $chemical->chemical_id,
                'alert_type' => $type,
                'message' => $message,
            ]);
        }
    }

    protected function recipientsFor(Chemical $chemical): Collection
    {
        return User::receivesAlerts()->get();
    }
}
