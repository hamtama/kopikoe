<?php

namespace App\Filament\Resources\JenisKopiResource\Pages;

use App\Filament\Resources\JenisKopiResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenisKopis extends ListRecords
{
    protected static string $resource = JenisKopiResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTitle(): string
    {
        return 'Jenis Kopi';
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url('/admin/jenis-kopis') => 'Jenis Kopi',
            url('#') => 'List',
        ];
    }
}
