<?php

namespace App\Filament\Resources\ParametersResource\Pages;

use App\Filament\Resources\ParametersResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParameters extends EditRecord
{
    protected static string $resource = ParametersResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
