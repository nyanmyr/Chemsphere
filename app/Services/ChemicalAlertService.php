<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\ChemicalBatch;
use App\Models\User;
use Illuminate\Support\Collection;

class ChemicalAlertService
{
    public function __construct(
        protected int $expiringSoonMonths = 3,
        protected float $lowStockThreshold = 0.2, // 20% of quantity_received
    ) {}

    public function checkChemical(ChemicalBatch $batch): void
    {
        $batch->loadMissing('chemical');
        $this->checkExpiration($batch);
        $this->checkStockLevel($batch);
    }

    public function expirationStatus(ChemicalBatch $batch): ?AlertType
    {
        if (!$batch->expiration_date) {
            return null;
        }

        $expiresAt = now()->parse($batch->expiration_date);

        if ($expiresAt->lte(today())) {
            return AlertType::EXPIRED;
        }

        if ($expiresAt->lte(today()->addMonthsNoOverflow($this->expiringSoonMonths))) {
            return AlertType::EXPIRING_SOON;
        }

        return null;
    }

    protected function checkExpiration(ChemicalBatch $batch): void
    {
        $status = $this->expirationStatus($batch);

        if ($status === null) {
            return;
        }

        $name = $batch->chemical?->chemical_name ?? 'Deleted chemical';
        $expiresOn = now()->parse($batch->expiration_date)->toFormattedDateString();

        $this->notify(
            $batch,
            $status,
            $status === AlertType::EXPIRED
                ? "{$name} (batch {$batch->batch_number}) expired on {$expiresOn}."
                : "{$name} (batch {$batch->batch_number}) expires on {$expiresOn}."
        );
    }

    public function expiringSoonMonths(): int
    {
        return $this->expiringSoonMonths;
    }

    protected function checkStockLevel(ChemicalBatch $batch): void
    {
        $name = $batch->chemical?->chemical_name ?? 'Deleted chemical';

        if ($batch->current_quantity <= 0) {
            $this->notify(
                $batch,
                AlertType::OUT_OF_STOCK,
                "{$name} (batch {$batch->batch_number}) is out of stock."
            );
            return;
        }

        $threshold = $batch->quantity_received * $this->lowStockThreshold;

        if ($batch->current_quantity <= $threshold) {
            $this->notify(
                $batch,
                AlertType::LOW_STOCK,
                "{$name} (batch {$batch->batch_number}) is low: {$batch->current_quantity} containers remaining."
            );
        }
    }

    protected function notify(ChemicalBatch $batch, AlertType $type, string $message): void
    {
        foreach ($this->recipientsFor($batch) as $recipient) {
            $existing = $recipient->alerts()
                ->where('item_id', $batch->chemical_batch_id)
                ->where('alert_type', $type->value)
                ->unread()
                ->first();

            if ($existing) {
                $existing->update(['message' => $message]);
                continue;
            }

            $recipient->alerts()->create([
                'item_id' => $batch->chemical_batch_id,
                'alert_type' => $type,
                'message' => $message,
            ]);
        }
    }

    protected function recipientsFor(ChemicalBatch $batch): Collection
    {
        return User::receivesAlerts()->get();
    }

    public function lowStockThreshold(): float
    {
        return $this->lowStockThreshold;
    }
}
