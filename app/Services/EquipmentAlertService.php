<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Support\Collection;

class EquipmentAlertService
{
    public function __construct(
        protected int $expiringSoonDays = 30,
    ) {}

    public function checkEquipment(Equipment $equipment): void
    {
        $this->checkWarranty($equipment);
    }

    protected function checkWarranty(Equipment $equipment): void
    {
        $expiresAt = $equipment->warranty_expiration;

        if (!$expiresAt) {
            return;
        }

        $expiresAt = now()->parse($expiresAt);

        if ($expiresAt->isPast()) {
            $this->notify(
                $equipment,
                AlertType::WARRANTY_EXPIRED,
                "{$equipment->equipment_name} warranty expired on {$expiresAt->toFormattedDateString()}."
            );
            return;
        }

        if (now()->diffInDays($expiresAt, false) <= $this->expiringSoonDays) {
            $this->notify(
                $equipment,
                AlertType::WARRANTY_EXPIRING_SOON,
                "{$equipment->equipment_name} warranty expires on {$expiresAt->toFormattedDateString()}."
            );
        }
    }

    protected function notify(Equipment $equipment, AlertType $type, string $message): void
    {
        foreach ($this->recipientsFor($equipment) as $recipient) {
            $existing = $recipient->alerts()
                ->where('item_id', $equipment->equipment_id)
                ->where('alert_type', $type->value)
                ->unread()
                ->first();

            if ($existing) {
                $existing->update(['message' => $message]);
                continue;
            }

            $recipient->alerts()->create([
                'item_id' => $equipment->equipment_id,
                'alert_type' => $type,
                'message' => $message,
            ]);
        }
    }

    protected function recipientsFor(Equipment $equipment): Collection
    {
        return User::receivesAlerts()->get();
    }
}
