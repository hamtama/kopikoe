<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VariableResource\Pages;
use App\Models\JenisKopi;
use App\Models\Variables;
use App\Models\Parameters;
use App\Models\SubParameter;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class VariableResource extends Resource
{
    protected static ?string $model = Variables::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static ?string $navigationLabel = 'Data Sampel Kopi';
    protected static ?string $navigationGroup = 'Data Supplier/Penggiat Kopi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $parameters = Parameters::with('subParameters')->get();
        $numericInputs = [];
        $multipleInputs = [];

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                $multipleInputs[] = CheckboxList::make("parameters.{$transformedName}")
                    ->label($parameter->name . ' (' . $parameter->kode . ')')
                    ->options($subParameters)
                    ->columns(3)
                    ->columnSpanFull();
            } else {
                $numericInputs[] = TextInput::make("parameters.{$transformedName}")
                    ->label($parameter->name . ' (' . $parameter->kode . ')')
                    ->helperText("Skala: {$parameter->min} - {$parameter->max} | Tipe: " . ucfirst($parameter->type))
                    ->required()
                    ->numeric()
                    ->rules(['min:' . $parameter->min, 'max:' . $parameter->max]);
            }
        }

        $cards = [
            Card::make()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Produk Kopi')
                                ->placeholder('Contoh: Arabika Gayo Wine Process')
                                ->required(),
                            TextInput::make('nama_supplier')
                                ->label('Nama Petani / Supplier')
                                ->placeholder('Contoh: Koperasi Petani Kopi Gayo')
                                ->required(),
                            Select::make('jenis_kopi')
                                ->label('Jenis Kopi')
                                ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi'))
                                ->required()
                                ->searchable(),
                            TextInput::make('alamat')
                                ->label('Lokasi / Asal Kebun')
                                ->placeholder('Contoh: Takengon, Aceh Tengah')
                                ->required(),
                        ]),
                    FileUpload::make('image')
                        ->label('Foto Sampel Kopi')
                        ->image()
                        ->multiple()
                        ->directory('variables')
                        ->storeFiles()
                        ->columnSpanFull(),
                ]),
        ];

        if (!empty($numericInputs)) {
            $cards[] = Card::make()
                ->schema([
                    Grid::make(3)
                        ->schema($numericInputs),
                ]);
        }

        if (!empty($multipleInputs)) {
            $cards[] = Card::make()
                ->schema($multipleInputs);
        }

        return $form->schema($cards);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\ImageColumn::make('image')
                ->label('Foto')
                ->getStateUsing(function ($record) {
                    $img = $record->image;
                    if (is_string($img)) {
                        $img = json_decode($img, true);
                    }
                    if (is_array($img) && !empty($img)) {
                        return $img[0];
                    }
                    return null;
                })
                ->disk('public')
                ->size(45)
                ->circular()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('name')
                ->label('Nama Kopi')
                ->searchable()
                ->sortable()
                ->weight('bold')
                ->description(fn ($record) => 'Supplier: ' . ($record->nama_supplier ?? '-')),

            Tables\Columns\BadgeColumn::make('JenisKopi.jenis_kopi')
                ->label('Jenis Kopi')
                ->color('primary')
                ->sortable(),
        ];

        $parameters = Parameters::all();
        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            $columns[] = Tables\Columns\BadgeColumn::make("param_{$transformedName}")
                ->label($parameter->kode)
                ->getStateUsing(function ($record) use ($transformedName, $parameter) {
                    if (!$record->parameters) {
                        return '-';
                    }

                    $params = is_array($record->parameters)
                        ? $record->parameters
                        : (json_decode($record->parameters ?? '[]', true) ?: []);

                    if (!is_array($params)) {
                        return '-';
                    }

                    $val = $params[$transformedName]
                        ?? $params[strtolower($transformedName)]
                        ?? $params[$parameter->parameter_id]
                        ?? $params[str_replace(' ', '_', strtolower(trim($parameter->name)))]
                        ?? null;

                    if ($val === null || $val === '') {
                        return '-';
                    }

                    if (is_array($val)) {
                        if (empty($val)) {
                            return '-';
                        }
                        static $subParamMap = null;
                        if ($subParamMap === null) {
                            $subParamMap = SubParameter::pluck('name', 'subparameter_id')->toArray();
                        }
                        $names = array_filter(array_map(fn($id) => $subParamMap[$id] ?? null, $val));
                        if (empty($names)) {
                            return count($val) . ' item';
                        }
                        return count($names) === 1 ? reset($names) : count($names) . ' pilihan';
                    }

                    return is_numeric($val) ? (string) (0 + $val) : (string) $val;
                })
                ->colors([
                    'secondary' => fn ($state): bool => $state === '-' || $state === null || $state === '',
                    'primary' => fn ($state): bool => $state !== '-' && $state !== null && $state !== '',
                ])
                ->tooltip(function ($record) use ($transformedName, $parameter) {
                    $baseTooltip = $parameter->name . ' (' . ucfirst($parameter->type ?? 'kriteria') . ')';
                    if (!$record || !$record->parameters) {
                        return $baseTooltip;
                    }

                    $params = is_array($record->parameters)
                        ? $record->parameters
                        : (json_decode($record->parameters ?? '[]', true) ?: []);

                    $val = $params[$transformedName]
                        ?? $params[strtolower($transformedName)]
                        ?? $params[$parameter->parameter_id]
                        ?? $params[str_replace(' ', '_', strtolower(trim($parameter->name)))]
                        ?? null;

                    if (is_array($val) && !empty($val)) {
                        static $subParamMap = null;
                        if ($subParamMap === null) {
                            $subParamMap = SubParameter::pluck('name', 'subparameter_id')->toArray();
                        }
                        $names = array_filter(array_map(fn($id) => $subParamMap[$id] ?? null, $val));
                        if (!empty($names)) {
                            return $parameter->name . ': ' . implode(', ', $names);
                        }
                    } elseif ($val !== null && $val !== '') {
                        return $parameter->name . ': ' . $val;
                    }

                    return $baseTooltip;
                })
                ->alignCenter();
        }

        $columns[] = Tables\Columns\BadgeColumn::make('status')
            ->label('Status')
            ->formatStateUsing(fn ($state) => $state ? 'Ternormalisasi' : 'Belum')
            ->colors([
                'success' => 1,
                'warning' => 0,
            ])
            ->icons([
                'heroicon-o-check-circle' => 1,
                'heroicon-o-clock' => 0,
            ])
            ->alignCenter();

        return $table
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_kopi')
                    ->label('Filter Jenis Kopi')
                    ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi')),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status Normalisasi')
                    ->trueLabel('Sudah Dinormalisasi')
                    ->falseLabel('Belum Dinormalisasi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('normalize')
                    ->label('Normalisasi')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->action(function ($record) {
                        if (!$record->isValid()) {
                            Notification::make()
                                ->title('Data Belum Lengkap')
                                ->body('Data parameter belum terisi lengkap sesuai kriteria.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->normalisasi = Variables::normalizeData($record->parameters);
                        $record->status = 1;
                        $record->save();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Data sampel kopi berhasil dinormalisasi ke skala [0, 1].')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status == 0),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkNormalize')
                    ->label('Normalisasi Terpilih')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->isValid()) {
                                $record->normalisasi = Variables::normalizeData($record->parameters);
                                $record->status = 1;
                                $record->save();
                                $count++;
                            }
                        }
                        Notification::make()
                            ->title('Normalisasi Selesai')
                            ->body("{$count} data sampel kopi berhasil dinormalisasi.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['JenisKopi'])
            ->orderBy('status', 'desc');
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['parameters']) && is_array($data['parameters'])) {
            foreach ($data['parameters'] as $key => $value) {
                if (is_array($value) && empty($value)) {
                    $data['parameters'][$key] = [0];
                }
            }
        }

        return $data;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVariables::route('/'),
            'create' => Pages\CreateVariable::route('/create'),
            'edit' => Pages\EditVariable::route('/{record}/edit'),
        ];
    }
}
