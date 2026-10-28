<?php

namespace App\Console\Commands;

use App\Models\ChemicalContainer;
use App\Services\ChemicalContainerAlertService;
use Illuminate\Console\Command;

class CheckChemicalAlerts extends Command
{
    protected $signature = 'chemicals:check-alerts';
    protected $description = 'Check chemical containers for expiration and low-stock conditions.';

    public function handle(ChemicalContainerAlertService $alertService): int
    {
        ChemicalContainer::query()->with('chemical')->chunk(100, function ($containers) use ($alertService) {
            $containers->each(fn (ChemicalContainer $container) => $alertService->checkContainer($container));
        });
        $this->info('Chemical alert check completed.');
        return self::SUCCESS;
    }
}
