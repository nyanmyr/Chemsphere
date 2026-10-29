<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ChemicalBatch;
use App\Models\Equipment;
use App\Observers\ChemicalsObserver;
use App\Observers\EquipmentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ChemicalBatch::observe(ChemicalsObserver::class);
        Equipment::observe(EquipmentObserver::class);
    }
}
