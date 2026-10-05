<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Chemical;
use App\Observers\ChemicalsObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Chemical::observe(ChemicalsObserver::class);
    }
}
