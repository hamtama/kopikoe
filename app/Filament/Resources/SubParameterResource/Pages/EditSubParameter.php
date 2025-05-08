<?php

namespace App\Filament\Resources\SubParameterResource\Pages;

use App\Filament\Resources\SubParameterResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubParameter extends EditRecord
{
    protected static string $resource = SubParameterResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
