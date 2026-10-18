<?php

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVariables extends ListRecords
{
    protected static string $resource = VariableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Sampel Kopi'),
        ];
    }
}
