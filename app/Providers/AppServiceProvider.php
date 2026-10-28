<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Equipment;
use App\Models\ChemicalContainer;
use App\Observers\EquipmentObserver;
use App\Observers\ChemicalContainersObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Equipment::observe(EquipmentObserver::class);
        ChemicalContainer::observe(ChemicalContainersObserver::class);
    }
}
