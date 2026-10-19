<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubParameterResource\Pages;
use App\Models\SubParameter;
use App\Models\Parameters;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SubParameterResource extends Resource
{
    protected static ?string $model = SubParameter::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-boards';
    protected static ?string $navigationLabel = 'Sub-Parameter / Pilihan';
    protected static ?string $navigationGroup = 'Data Resource';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('parameter_id')
                                    ->label('Parameter Induk')
                                    ->options(Parameters::where('is_multiple', true)->pluck('name', 'parameter_id'))
                                    ->required()
                                    ->searchable()
                                    ->helperText('Hanya kriteria dengan format multi-kategori yang muncul.'),

                                TextInput::make('name')
                                    ->label('Nama Karakteristik / Sub-Kategori')
                                    ->placeholder('Contoh: Floral, Spicy, Citrus, Caramel')
                                    ->required(),

                                TextInput::make('bobot')
                                    ->label('Nilai Bobot')
                                    ->type('number')
                                    ->default(1)
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1)
                                    ->required()
                                    ->helperText('Bobot nilai numerik untuk perhitungan.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('parameters.kode')
                    ->label('Kode')
                    ->color('secondary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('parameters.name')
                    ->label('Parameter Induk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Karakteristik')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('bobot')
                    ->label('Bobot Nilai')
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parameter_id')
                    ->label('Filter Parameter')
                    ->options(Parameters::where('is_multiple', true)->pluck('name', 'parameter_id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['parameters']);
    }

    public static function getRelations(): array
    {
        return [];
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
