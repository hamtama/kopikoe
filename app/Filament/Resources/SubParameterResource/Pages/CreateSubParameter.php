<?php

namespace App\Filament\Resources\SubParameterResource\Pages;

use App\Filament\Resources\SubParameterResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Parameters;

class CreateSubParameter extends CreateRecord
{
    protected static string $resource = SubParameterResource::class;

    protected function getFormSchema(): array
    {
        $parameterId = request()->route('parameter');

        return [
            Select::make('parameter_id')
                ->label('Parameter')
                ->options(Parameters::all()->pluck('name', 'id'))
                ->default($parameterId)
                ->disabled(),
            TextInput::make('name')->required(),
        ];
    }
}
