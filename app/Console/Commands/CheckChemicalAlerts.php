<?php

namespace App\Console\Commands;

use App\Models\Chemical;
use App\Services\ChemicalAlertService;
use Illuminate\Console\Command;

class CheckChemicalAlerts extends Command
{
    protected $signature = 'chemicals:check-alerts';
    protected $description = 'Check chemicals for expiration and low-stock conditions.';

    public function handle(ChemicalAlertService $alertService): int
    {
        Chemical::query()->chunk(100, function ($chemicals) use ($alertService) {
            $chemicals->each(fn (Chemical $c) => $alertService->checkChemical($c));
        });

        $this->info('Chemical alert check completed.');
        return self::SUCCESS;
    }
}
