<?php

namespace App\Console\Commands;

use App\Models\ChemicalBatch;
use App\Services\ChemicalAlertService;
use Illuminate\Console\Command;

class CheckChemicalAlerts extends Command
{
    protected $signature = 'chemicals:check-alerts';
    protected $description = 'Check chemical batches for expiration and low-stock conditions.';

    public function handle(ChemicalAlertService $alertService): int
    {
        ChemicalBatch::query()->with('chemical')->chunk(100, function ($batches) use ($alertService) {
            $batches->each(fn (ChemicalBatch $batch) => $alertService->checkChemical($batch));
        });

        $this->info('Chemical batch alert check completed.');
        return self::SUCCESS;
    }
}
