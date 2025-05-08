<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubParameterResource\Pages;
use App\Models\SubParameter;
use App\Models\Parameters;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;


class SubParameterResource extends Resource
{
    protected static ?string $model = SubParameter::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Data Resource';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parameter_id')
                    ->label('Parameter')
                    ->options(Parameters::where('is_multiple', true)->pluck('name', 'parameter_id'))
                    ->required(),
                Forms\Components\TextInput::make('name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parameters.name')->label('Parameter')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubParameters::route('/'),
            'create' => Pages\CreateSubParameter::route('/create'),
            'edit' => Pages\EditSubParameter::route('/{record}/edit'),
        ];
    }
}
