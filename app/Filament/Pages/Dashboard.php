<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use App\Filament\Widgets\DashboardHero;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\CoffeeVarietyChart;
use App\Filament\Widgets\SimulationTrendChart;
use App\Filament\Widgets\LatestSimulationsWidget;

class Dashboard extends BasePage
{
    protected function getWidgets(): array
    {
        return [
            DashboardHero::class,
            StatsOverview::class,
            CoffeeVarietyChart::class,
            SimulationTrendChart::class,
            LatestSimulationsWidget::class,
        ];
    }

    protected function getColumns(): int | array
    {
        return 2;
    }
}
