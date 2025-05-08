<?php

namespace App\Filament\Resources\NormalizationResource\Pages;

use App\Filament\Resources\NormalizationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNormalization extends EditRecord
{
    protected static string $resource = NormalizationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
