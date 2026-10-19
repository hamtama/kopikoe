<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Variables;
use App\Models\Simulation;
use App\Models\Parameters;
use Illuminate\Support\Facades\Auth;

class DashboardHero extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-hero';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();
        $hour = (int) date('H');

        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
            $icon = '☀️';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
            $icon = '☕';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
            $icon = '🌤️';
        } else {
            $greeting = 'Selamat Malam';
            $icon = '🌙';
        }

        $totalKopi = Variables::count();
        $readyKopi = Variables::where('status', 1)->count();
        $percentReady = $totalKopi > 0 ? round(($readyKopi / $totalKopi) * 100) : 0;

        return [
            'greeting' => $greeting,
            'greetingIcon' => $icon,
            'userName' => $user->name ?? 'Administrator',
            'userRole' => ($user->is_admin ?? false) ? 'Super Administrator' : 'Pengelola Kopi',
            'totalKopi' => $totalKopi,
            'readyKopi' => $readyKopi,
            'percentReady' => $percentReady,
            'totalSimulasi' => Simulation::count(),
            'totalKriteria' => Parameters::count(),
        ];
    }
}
