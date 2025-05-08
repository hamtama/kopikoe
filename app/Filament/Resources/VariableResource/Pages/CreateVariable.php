<?php

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Variables;
use Illuminate\Support\Facades\Log;

class CreateVariable extends CreateRecord
{
    protected static string $resource = VariableResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
