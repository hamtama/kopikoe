<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Filament::serving(function () {
        //     // Using Vite
        //     Filament::registerViteTheme('resources/css/filament.css');
        // });
        // Mendaftarkan komponen Blade
        Blade::component('components.level-input', 'form.level-input');
    }
}
