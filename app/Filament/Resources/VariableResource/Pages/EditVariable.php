<?php

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Variables;

class EditVariable extends EditRecord
{
    protected static string $resource = VariableResource::class;

    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        $parameters = [];
        foreach ($data['parameters'] as $key => $value) {
            $parameters[$key] = $value;
        }
        $data['parameters'] = $parameters;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
