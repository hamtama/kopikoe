<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use App\Filament\Pages\SimulateKNN;


class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::registerPages([
            SimulateKNN::class,
        ]);

        Filament::registerNavigationGroups([
            'Data Resource',
            'Data Supplier/Penggiat Kopi',
            'Data Testing Collection',
            'Users',
        ]);
    }
}
