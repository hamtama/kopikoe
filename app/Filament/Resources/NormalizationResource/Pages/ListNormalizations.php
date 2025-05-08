<?php

namespace App\Filament\Resources\NormalizationResource\Pages;

use App\Filament\Resources\NormalizationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNormalizations extends ListRecords
{
    protected static string $resource = NormalizationResource::class;

    protected function getTitle(): string
    {
        return 'Data Normalisasi';
    }



    protected function getBreadcrumbs(): array
    {
        return [
            url('/admin/normalizations') => 'Normalisasi',
            url('#') => 'List',
        ];
    }




    // protected function getActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
