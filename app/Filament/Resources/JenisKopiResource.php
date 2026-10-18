<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisKopiResource\Pages;
use App\Models\JenisKopi;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class JenisKopiResource extends Resource
{
    protected static ?string $model = JenisKopi::class;

    protected static ?string $navigationLabel = 'Jenis Kopi';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Data Resource';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->title('Data Varietas / Jenis Kopi')
                    ->description('Master data jenis kopi (Arabika, Robusta, Liberika, Ekselsa, dll).')
                    ->schema([
                        TextInput::make('jenis_kopi')
                            ->required()
                            ->label('Nama Jenis Kopi')
                            ->placeholder('Contoh: Arabika')
                            ->unique(ignorable: fn ($record) => $record),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_jenis_kopi')
                    ->sortable()
                    ->label('#'),

                Tables\Columns\TextColumn::make('jenis_kopi')
                    ->searchable()
                    ->sortable()
                    ->label('Jenis / Varietas Kopi')
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('variables_count')
                    ->counts('variables')
                    ->label('Jumlah Sampel Terdaftar')
                    ->color('primary')
                    ->alignCenter(),
            ])
            ->filters([
                //
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
            ->withCount('variables');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisKopis::route('/'),
            'create' => Pages\CreateJenisKopi::route('/create'),
            'edit' => Pages\EditJenisKopi::route('/{record}/edit'),
        ];
    }
}
