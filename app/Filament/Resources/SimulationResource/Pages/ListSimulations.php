<?php

namespace App\Filament\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimulations extends ListRecords
{
    protected static string $resource = SimulationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
