<?php

namespace App\Filament\Resources\JenisKopiResource\Pages;

use App\Filament\Resources\JenisKopiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisKopi extends CreateRecord
{
    protected static string $resource = JenisKopiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
