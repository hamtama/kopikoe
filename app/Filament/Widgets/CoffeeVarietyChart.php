<?php

namespace App\Filament\Widgets;

use App\Models\JenisKopi;
use Filament\Widgets\DoughnutChartWidget;

class CoffeeVarietyChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Distribusi Sampel Berdasarkan Varietas Kopi';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'sm' => 1,
        'lg' => 1,
    ];

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $varieties = JenisKopi::withCount('variables')->get();

        $labels = [];
        $data = [];
        foreach ($varieties as $v) {
            $labels[] = $v->jenis_kopi . " ({$v->variables_count})";
            $data[] = $v->variables_count;
        }

        if (empty($data) || array_sum($data) === 0) {
            $labels = ['Belum ada sampel'];
            $data = [1];
            $colors = ['#E2E8F0'];
        } else {
            $colors = [
                '#C67C4E', // Caramel / Light roast
                '#8C6D58', // Coffee bean brown
                '#4A3E3D', // Dark roast espresso
                '#D4A373', // Latte cream
                '#2D2424', // Deep roast
                '#E0A96D', // Honey process amber
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Sampel',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
