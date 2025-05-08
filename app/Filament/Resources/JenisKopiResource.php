<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisKopiResource\Pages;
use App\Filament\Resources\JenisKopiResource\RelationManagers;
use App\Models\JenisKopi;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisKopiResource extends Resource
{
    protected static ?string $model = JenisKopi::class;

    protected static ?string $navigationLabel = 'Jenis Kopi';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Data Resource';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jenis_kopi')
                    ->required()
                    ->label('Jenis Kopi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_jenis_kopi')
                    ->sortable()
                    ->label('ID'),

                Tables\Columns\TextColumn::make('jenis_kopi')
                    ->searchable()
                    ->sortable()
                    ->label('Jenis Kopi')

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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
