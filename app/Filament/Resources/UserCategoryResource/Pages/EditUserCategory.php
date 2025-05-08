<?php

namespace App\Filament\Resources\UserCategoryResource\Pages;

use App\Filament\Resources\UserCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserCategory extends EditRecord
{
    protected static string $resource = UserCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
