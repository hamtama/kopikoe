<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserCategoryResource\Pages;
use App\Models\UserCategory;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UserCategoryResource extends Resource
{
    protected static ?string $model = UserCategory::class;

    protected static ?string $navigationLabel = 'Kategori Pengguna';
    protected static ?string $navigationGroup = 'Users';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name_user_category')
                            ->label('Nama Kategori Pengguna')
                            ->placeholder('Contoh: Penggiat Kopi, Pencari Kopi')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_user_category')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserCategories::route('/'),
            'create' => Pages\CreateUserCategory::route('/create'),
            'edit' => Pages\EditUserCategory::route('/{record}/edit'),
        ];
    }
}
