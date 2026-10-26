<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Support\Collection;

class EquipmentAlertService
{
    public function __construct(
        protected int $closeToDueMonths = 3,
    ) {}

    public function checkEquipment(Equipment $equipment): void
    {
        $this->checkMaintenance($equipment);
    }

    public function maintenanceStatus(Equipment $equipment): ?AlertType
    {
        if (!$equipment->next_due) {
            return null;
        }

        $dueOn = now()->parse($equipment->next_due);

        if ($dueOn->lte(today())) {
            return AlertType::MAINTENANCE_OVERDUE;
        }

        if ($dueOn->lte(today()->addMonthsNoOverflow($this->closeToDueMonths))) {
            return AlertType::MAINTENANCE_CLOSE_TO_DUE;
        }

        return null;
    }

    protected function checkMaintenance(Equipment $equipment): void
    {
        $status = $this->maintenanceStatus($equipment);

        if ($status === null) {
            return;
        }

        $dueOn = $equipment->next_due->toFormattedDateString();

        $this->notify(
            $equipment,
            $status,
            $status === AlertType::MAINTENANCE_OVERDUE
                ? "{$equipment->equipment_name} is overdue for preventive maintenance (due {$dueOn})."
                : "{$equipment->equipment_name} is due for preventive maintenance on {$dueOn}."
        );
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

    public function closeToDueMonths(): int
    {
        return $this->closeToDueMonths;
    }
}