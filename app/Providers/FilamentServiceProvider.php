<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Livewire\Livewire;
use App\Filament\Pages\SimulateKNN;
use App\Filament\Widgets\DashboardHero;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\CoffeeVarietyChart;
use App\Filament\Widgets\SimulationTrendChart;
use App\Filament\Widgets\LatestSimulationsWidget;

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

        // Daftarkan widget ke Livewire secara eksplisit untuk mencegah ComponentNotFoundException
        Livewire::component(DashboardHero::getName(), DashboardHero::class);
        Livewire::component(StatsOverview::getName(), StatsOverview::class);
        Livewire::component(CoffeeVarietyChart::getName(), CoffeeVarietyChart::class);
        Livewire::component(SimulationTrendChart::getName(), SimulationTrendChart::class);
        Livewire::component(LatestSimulationsWidget::getName(), LatestSimulationsWidget::class);
    }
}
