<?php

namespace App\Filament\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimulation extends EditRecord
{
    protected static string $resource = SimulationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
