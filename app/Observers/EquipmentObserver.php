<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Services\EquipmentAlertService;

class EquipmentObserver
{
    public function __construct(protected EquipmentAlertService $alertService) {}

    public function created(Equipment $equipment): void
    {
        $this->alertService->checkEquipment($equipment);
    }

    public function updated(Equipment $equipment): void
    {
        if ($equipment->wasChanged(['warranty_expiration'])) {
            $this->alertService->checkEquipment($equipment);
        }
    }
}
