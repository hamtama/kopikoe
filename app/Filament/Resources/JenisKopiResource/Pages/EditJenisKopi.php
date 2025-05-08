<?php

namespace App\Filament\Resources\JenisKopiResource\Pages;

use App\Filament\Resources\JenisKopiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJenisKopi extends EditRecord
{
    protected static string $resource = JenisKopiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
