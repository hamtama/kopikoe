<?php

namespace App\Filament\Widgets;

use App\Models\Variables;
use App\Models\Simulation;
use App\Models\Parameters;
use App\Models\SubParameter;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getCards(): array
    {
        $totalKopi = Variables::count();
        $normalizedKopi = Variables::where('status', 1)->count();
        $percentReady = $totalKopi > 0 ? round(($normalizedKopi / $totalKopi) * 100) : 0;

        $totalSim = Simulation::count();
        $todaySim = Simulation::whereDate('created_at', Carbon::today())->count();

        $totalParam = Parameters::count();
        $totalSubParam = SubParameter::count();

        $totalUsers = User::count();
        $regularMembers = User::where('is_admin', 0)->count();

        // Tren simulasi 7 hari terakhir untuk sparkline
        $simTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $simTrend[] = Simulation::whereDate('created_at', $date)->count();
        }

        return [
            Card::make('Total Sampel Kopi', $totalKopi . ' Sampel')
                ->description($normalizedKopi . " siap uji ({$percentReady}% ternormalisasi)")
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success')
                ->chart([3, 5, 4, 8, 7, 9, max(1, $totalKopi)]),

            Card::make('Pengujian KNN', $totalSim . ' Simulasi')
                ->description($todaySim . ' pengujian dilakukan hari ini')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('primary')
                ->chart($simTrend),

            Card::make('Parameter Sensori', $totalParam . ' Kriteria')
                ->description($totalSubParam . ' sub-karakteristik rasa terdaftar')
                ->descriptionIcon('heroicon-s-adjustments')
                ->color('warning'),

            Card::make('Pengguna Terdaftar', $totalUsers . ' Akun')
                ->description($regularMembers . ' penggiat & pencari kopi aktif')
                ->descriptionIcon('heroicon-s-users')
                ->color('secondary'),
        ];
    }
}
