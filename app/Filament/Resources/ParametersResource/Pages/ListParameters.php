<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParameters extends ListRecords
{
    protected static string $resource = ParametersResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
