<?php

namespace App\Filament\Widgets;

use App\Models\Simulation;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class SimulationTrendChart extends LineChartWidget
{
    protected static ?string $heading = 'Aktivitas Pengujian KNN (7 Hari Terakhir)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'lg' => 1,
    ];

    protected static ?string $maxHeight = '280px';

    public static function getName(): string
    {
        return 'app.filament.widgets.simulation-trend-chart';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = Simulation::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Simulasi',
                    'data' => $data,
                    'borderColor' => '#C67C4E',
                    'backgroundColor' => 'rgba(198, 124, 78, 0.15)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
