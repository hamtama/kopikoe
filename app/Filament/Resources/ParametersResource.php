<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParametersResource\Pages;
use App\Models\Parameters;
use App\Models\SubParameter;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ParametersResource extends Resource
{
    protected static ?string $model = Parameters::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments';
    protected static ?string $navigationLabel = 'Kriteria / Parameter';
    protected static ?string $navigationGroup = 'Data Resource';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->title('Konfigurasi Parameter / Kriteria KNN')
                    ->description('Tentukan kriteria penilaian sensori dan aturan normalisasi Min-Max.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('kode')
                                    ->label('Kode Kriteria')
                                    ->default(fn () => Parameters::generateKode())
                                    ->required()
                                    ->unique(ignorable: fn ($record) => $record)
                                    ->extraAttributes(['readonly' => true])
                                    ->helperText('Kode otomatis terbuat (C1, C2, dst).'),

                                TextInput::make('name')
                                    ->label('Nama Kriteria')
                                    ->placeholder('Contoh: Acidity / Keasaman')
                                    ->required(),

                                Select::make('type')
                                    ->label('Tipe Kriteria Normalisasi')
                                    ->options([
                                        'benefit' => 'Benefit (Semakin tinggi nilai semakin baik)',
                                        'cost' => 'Cost (Semakin rendah nilai semakin baik)',
                                    ])
                                    ->required()
                                    ->helperText('Rumus Benefit: (x - min) / (max - min) | Rumus Cost: (max - x) / (max - min)'),

                                Toggle::make('is_multiple')
                                    ->label('Multi Sub-Kategori (Pilihan Ganda)')
                                    ->helperText('Aktifkan jika kriteria ini memiliki sub-kategori seperti notes rasa.')
                                    ->default(false)
                                    ->inline(false),

                                TextInput::make('min')
                                    ->label('Nilai Minimum (Batas Bawah)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->helperText('Nilai minimum absolut untuk normalisasi.'),

                                TextInput::make('max')
                                    ->label('Nilai Maksimum (Batas Atas)')
                                    ->numeric()
                                    ->default(100)
                                    ->required()
                                    ->helperText('Nilai batas maksimum untuk skala kriteria.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('kode')
                    ->label('Kode')
                    ->color('secondary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kriteria')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->enum([
                        'benefit' => 'Benefit (+)',
                        'cost' => 'Cost (-)',
                    ])
                    ->colors([
                        'success' => 'benefit',
                        'danger' => 'cost',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('range')
                    ->label('Rentang Skala')
                    ->getStateUsing(fn ($record) => "{$record->min} — {$record->max}")
                    ->alignCenter(),

                Tables\Columns\BadgeColumn::make('is_multiple')
                    ->label('Format Input')
                    ->formatStateUsing(fn ($state) => $state ? 'Multi Sub-Kategori' : 'Numerik Tunggal')
                    ->colors([
                        'warning' => fn ($state) => $state,
                        'primary' => fn ($state) => !$state,
                    ]),

                Tables\Columns\TextColumn::make('sub_summary')
                    ->label('Sub-Kategori')
                    ->getStateUsing(function ($record) {
                        if (!$record->is_multiple) {
                            return '— (Skala Angka)';
                        }
                        $count = $record->subParameters->count();
                        if ($count === 0) {
                            return 'Belum ada sub-kategori';
                        }
                        return "{$count} Pilihan: " . $record->subParameters->pluck('name')->join(', ');
                    })
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->is_multiple ? $record->subParameters->pluck('name')->join(', ') : null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Kriteria')
                    ->options([
                        'benefit' => 'Benefit',
                        'cost' => 'Cost',
                    ]),
                Tables\Filters\TernaryFilter::make('is_multiple')
                    ->label('Format Input')
                    ->trueLabel('Multi Sub-Kategori')
                    ->falseLabel('Numerik Tunggal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('addSubParameter')
                    ->label('+ Sub-Kategori')
                    ->icon('heroicon-o-plus-circle')
                    ->color('secondary')
                    ->form(fn ($record) => [
                        Hidden::make('parameter_id')->default($record->parameter_id),
                        TextInput::make('name')
                            ->label('Nama Sub-Kategori')
                            ->placeholder('Contoh: Fruity, Citric, Chocolaty')
                            ->required(),
                        TextInput::make('bobot')
                            ->label('Bobot Nilai')
                            ->type('number')
                            ->default(1)
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->helperText('Bobot nilai saat opsi ini dipilih.'),
                    ])
                    ->action(function ($data) {
                        SubParameter::create([
                            'parameter_id' => $data['parameter_id'],
                            'name' => $data['name'],
                            'bobot' => $data['bobot'],
                        ]);
                        Notification::make()
                            ->title('Sub-kategori berhasil ditambahkan')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Tambah Sub-Kategori Baru')
                    ->modalButton('Simpan')
                    ->visible(fn ($record) => $record->is_multiple),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['subParameters']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameters::route('/create'),
            'edit' => Pages\EditParameters::route('/{record}/edit'),
        ];
    }
}
