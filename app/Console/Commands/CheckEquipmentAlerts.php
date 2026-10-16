<?php

namespace App\Console\Commands;

use App\Models\Equipment;
use App\Services\EquipmentAlertService;
use Illuminate\Console\Command;

class CheckEquipmentAlerts extends Command
{
    protected $signature = 'equipment:check-alerts';

    protected $description = 'Check equipment for warranty expiration.';

    public function handle(EquipmentAlertService $alertService): int
    {
        Equipment::query()->chunk(100, function ($equipment) use ($alertService) {
            $equipment->each(fn (Equipment $c) => $alertService->checkEquipment($c));
        });

        $this->info('Equipment alert check completed.');
        return self::SUCCESS;
    }
}
