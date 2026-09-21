<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Chemical;
use App\Observers\ChemicalsObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Chemical::observe(ChemicalsObserver::class);
    }
}
