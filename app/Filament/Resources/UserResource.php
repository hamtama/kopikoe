<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\UserCategory;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->title('Data Akun Pengguna')
                    ->description('Kelola akun login admin, penggiat kopi, dan pencari kopi.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Nama lengkap user')
                                    ->required(),

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->email()
                                    ->placeholder('email@contoh.com')
                                    ->unique(ignorable: fn ($record) => $record)
                                    ->required(),

                                TextInput::make('password')
                                    ->label('Password Akun')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                                    ->helperText('Kosongkan jika tidak ingin mengubah password saat edit.'),

                                Select::make('user_category_id')
                                    ->label('Kategori Pengguna')
                                    ->relationship('category', 'name_user_category')
                                    ->required()
                                    ->searchable(),

                                Toggle::make('is_admin')
                                    ->label('Hak Akses Administrator')
                                    ->helperText('Jika aktif, pengguna dapat mengakses panel dashboard admin Filament ini.')
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('category.name_user_category')
                    ->label('Kategori')
                    ->color('secondary')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('is_admin')
                    ->label('Role')
                    ->formatStateUsing(fn ($state) => $state ? 'Admin' : 'Member')
                    ->colors([
                        'danger' => 1,
                        'success' => 0,
                    ])
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_admin')
                    ->label('Status Admin')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name_user_category'),
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Hak Akses Admin')
                    ->trueLabel('Hanya Admin')
                    ->falseLabel('Bukan Admin'),
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
            ->with(['category']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
