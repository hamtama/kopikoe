<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NormalizationResource\Pages;
use App\Filament\Resources\NormalizationResource\RelationManagers;
use App\Models\Variables;
use App\Models\Parameters;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NormalizationResource extends Resource
{
    protected static ?string $model = Variables::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = 'Data Normalisasi';
    protected static ?string $navigationGroup = 'Data Testing Collection';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'normalizations';


    // protected static ?string $navigationGroup = 'Normalization';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('name')->label('Nama Kopi'),
        ];

        $parameters = Parameters::all();
        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            $columns[] = Tables\Columns\TextColumn::make("normalisasi->{$transformedName}")
                ->label($parameter->kode)
                ->formatStateUsing(function ($state, $record) use ($transformedName) {
                    if (!$record->normalisasi) {
                        return '0';
                    }

                    $normalized = is_array($record->normalisasi) ? $record->normalisasi : json_decode($record->normalisasi, true);

                    if (is_array($normalized)) {
                        $values = array_filter($normalized, function ($key) use ($transformedName) {
                            return strpos($key, $transformedName) === 0;
                        }, ARRAY_FILTER_USE_KEY);

                        if (!empty($values)) {
                            return implode(', ', $values);
                        }
                    }

                    return '0';
                });
        }

        // Tambahkan kolom status sebagai ikon
        $columns[] = Tables\Columns\IconColumn::make('status')
            ->boolean()
            ->label('Status')
            ->trueIcon('heroicon-o-check-circle')
            ->falseIcon('heroicon-o-x-circle')
            ->alignCenter();

        return $table
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('status', 'desc'); // Sort by 'status' column, descending order
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
            'index' => Pages\ListNormalizations::route('/'),
            // 'create' => Pages\CreateNormalization::route('/create'),
            // 'edit' => Pages\EditNormalization::route('/{record}/edit'),
        ];
    }
}
