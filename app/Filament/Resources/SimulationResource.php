<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimulationResource\Pages;
use App\Models\Parameters;
use App\Models\SubParameter;
use App\Models\Simulation;
use App\Models\Variables;
use App\Models\JenisKopi;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SimulationResource extends Resource
{
    protected static ?string $model = Simulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-report';
    protected static ?string $navigationLabel = 'Riwayat Simulasi';
    protected static ?string $navigationGroup = 'Data Testing Collection';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $parameters = Parameters::with('subParameters')->get();
        $numericInputs = [];
        $multipleInputs = [];

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                $multipleInputs[] = CheckboxList::make($transformedName)
                    ->label($parameter->name . ' (' . $parameter->kode . ')')
                    ->options($subParameters)
                    ->columns(3)
                    ->columnSpanFull();
            } else {
                $numericInputs[] = TextInput::make($transformedName)
                    ->label($parameter->name . ' (' . $parameter->kode . ')')
                    ->helperText("Skala: {$parameter->min} - {$parameter->max}")
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
                                ->label('Nama Skenario / Penguji')
                                ->default(fn () => 'Pengujian ' . date('d/m/Y H:i'))
                                ->required(),
                            Select::make('jenis_kopi')
                                ->label('Target Jenis Kopi')
                                ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi'))
                                ->required()
                                ->searchable(),
                        ]),
                ]),
        ];

        if (!empty($numericInputs)) {
            $cards[] = Card::make()
                ->schema([
                    Grid::make(3)->schema($numericInputs),
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('input_data.name')
                    ->label('Skenario / Penguji')
                    ->getStateUsing(fn ($record) => $record->input_data['name'] ?? ('Simulasi #' . $record->id))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('input_data', 'like', "%{$search}%");
                    })
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('target_kopi')
                    ->label('Jenis Kopi')
                    ->getStateUsing(function ($record) {
                        $jkId = $record->input_data['jenis_kopi'] ?? null;
                        if (!$jkId) return '-';
                        $jk = JenisKopi::find($jkId);
                        return $jk ? $jk->jenis_kopi : "ID: {$jkId}";
                    })
                    ->color('primary'),

                Tables\Columns\TextColumn::make('top_recommendation')
                    ->label('Rekomendasi Utama (#1)')
                    ->getStateUsing(function ($record) {
                        $recs = $record->recommendations;
                        if (empty($recs) || !is_array($recs)) return 'Belum ada rekomendasi';
                        $top = $recs[0] ?? null;
                        if (!$top) return '-';
                        $supplier = !empty($top['nama_supplier']) ? ' (' . $top['nama_supplier'] . ')' : '';
                        return ($top['name'] ?? 'Kopi') . $supplier;
                    })
                    ->icon('heroicon-o-sparkles')
                    ->weight('medium'),

                Tables\Columns\BadgeColumn::make('similarity_score')
                    ->label('Tingkat Kemiripan')
                    ->getStateUsing(function ($record) {
                        $recs = $record->recommendations;
                        if (empty($recs) || !is_array($recs)) return '-';
                        $distance = $recs[0]['distance'] ?? null;
                        if ($distance === null) return '-';
                        $sim = max(0, min(100, round((1 / (1 + (float)$distance)) * 100, 1)));
                        return "{$sim}% (d: {$distance})";
                    })
                    ->colors([
                        'success' => fn ($state): bool => str_contains($state, '%') && (float)$state >= 80,
                        'warning' => fn ($state): bool => str_contains($state, '%') && (float)$state >= 60 && (float)$state < 80,
                        'danger' => fn ($state): bool => str_contains($state, '%') && (float)$state < 60,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Simulasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make('viewDetails')
                    ->label('Detail Rekomendasi')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Rincian Hasil Simulasi KNN')
                    ->modalContent(function ($record) {
                        $recs = $record->recommendations ?? [];
                        $input = $record->input_data ?? [];
                        return view('filament.components.simulation-detail-modal', [
                            'record' => $record,
                            'recs' => $recs,
                            'input' => $input,
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function normalizeData($inputData)
    {
        $normalized = [];
        $normalized['jenis_kopi'] = $inputData['jenis_kopi'] ?? null;
        $parameters = Parameters::with('subParameters')->get();

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if (isset($inputData[$transformedName])) {
                $value = $inputData[$transformedName];

                if ($parameter->is_multiple) {
                    $subParameters = SubParameter::where('parameter_id', $parameter->parameter_id)->pluck('subparameter_id')->toArray();
                    foreach ($subParameters as $subParameter) {
                        $normalized["{$transformedName}_{$subParameter}"] = in_array($subParameter, (array)$value) ? 1 : 0;
                    }
                } else {
                    $min = (float)$parameter->min;
                    $max = (float)$parameter->max;
                    $range = $max - $min;
                    if ($range <= 0) $range = 1;
                    $numVal = (float)$value;

                    if ($parameter->type == 'benefit') {
                        $normalized[$transformedName] = ($numVal - $min) / $range;
                    } else {
                        $normalized[$transformedName] = ($max - $numVal) / $range;
                    }
                }
            } else {
                if ($parameter->is_multiple) {
                    $subParameters = SubParameter::where('parameter_id', $parameter->parameter_id)->pluck('subparameter_id')->toArray();
                    foreach ($subParameters as $subParameter) {
                        $normalized["{$transformedName}_{$subParameter}"] = 0;
                    }
                } else {
                    $normalized[$transformedName] = 0;
                }
            }
        }

        return $normalized;
    }

    public static function calculateKNN($normalizedData)
    {
        if (empty($normalizedData['jenis_kopi'])) {
            return [];
        }

        $variables = Variables::where('jenis_kopi', $normalizedData['jenis_kopi'])->get();
        $distances = [];

        foreach ($variables as $variable) {
            $varNorm = $variable->normalisasi;
            if (empty($varNorm) || !is_array($varNorm)) {
                $varNorm = Variables::normalizeData($variable->parameters);
            }

            $sumSq = 0;
            foreach ($normalizedData as $key => $val) {
                if ($key === 'jenis_kopi') continue;
                $vVal = isset($varNorm[$key]) ? (float)$varNorm[$key] : 0.0;
                $uVal = (float)$val;
                $sumSq += pow($uVal - $vVal, 2);
            }

            $distance = sqrt($sumSq);
            $distances[] = [
                'variable' => $variable,
                'distance' => round($distance, 4),
            ];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $top = array_slice($distances, 0, 3);
        return array_map(function ($item) {
            $data = $item['variable']->toArray();
            $data['distance'] = $item['distance'];
            return $data;
        }, $top);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimulations::route('/'),
            'create' => Pages\CreateSimulation::route('/create'),
            'edit' => Pages\EditSimulation::route('/{record}/edit'),
        ];
    }
}
