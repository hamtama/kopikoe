<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimulationResource\Pages;
use Filament\Forms\Components\Select;
use App\Models\Parameters;
use App\Models\SubParameter;
use App\Models\Simulation;
use App\Models\Variables;
use App\Models\JenisKopi;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SimulationResource extends Resource
{
    protected static ?string $model = Simulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Data Testing Collection';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        $parameters = Parameters::with('subParameters')->get();
        $parameterInputs = [];

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                $parameterInputs[] = Forms\Components\CheckboxList::make("input_data.{$transformedName}")
                    ->label($parameter->name)
                    ->options($subParameters);
            } else {
                $parameterInputs[] = Forms\Components\TextInput::make("input_data.{$transformedName}")
                    ->label($parameter->name)
                    ->required()
                    ->numeric()
                    ->rules(['min:' . $parameter->min, 'max:' . $parameter->max]);
            }
        }

        return $form->schema(array_merge([
            Forms\Components\TextInput::make('input_data.name')->required(),
            Select::make('input_data.jenis_kopi')
                ->label('Jenis Kopi')
                ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi'))->required()
        ], $parameterInputs));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('name')->label('Nama Simulasi'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Simulasi')->date(),
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


    public static function normalizeData($inputData)
    {

        $normalized = [];
        $normalized['jenis_kopi'] = $inputData['jenis_kopi'];
        $parameters = Parameters::with('subParameters')->get();
        // dd($parameters);

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if (isset($inputData[$transformedName])) {
                $value = $inputData[$transformedName];

                if ($parameter->is_multiple) {
                    $subParameters = SubParameter::where('parameter_id', $parameter->parameter_id)->pluck('subparameter_id')->toArray();
                    foreach ($subParameters as $subParameter) {
                        $normalized["{$transformedName}_{$subParameter}"] = in_array($subParameter, $value) ? 1 : 0;
                    }
                } else {
                    $min = $parameter->min;
                    $max = $parameter->max;
                    if ($parameter->type == 'benefit') {
                        $normalized[$transformedName] = ($value - $min) / ($max - $min);
                    } else {
                        $normalized[$transformedName] = ($max - $value) / ($max - $min);
                    }
                }
            } else {
                // Handle missing parameters
                $normalized[$transformedName] = 0;
            }
        }

        // dd($normalized);
        return $normalized;
    }

    public static function calculateKNN($normalizedData)
    {
        if (!isset($normalizedData['jenis_kopi'])) {
            return []; // Pastikan ada data jenis kopi sebelum melanjutkan
        }
        // dd($normalizedData['jenis_kopi']);

        $variables = Variables::where('jenis_kopi', $normalizedData['jenis_kopi'])->get();
        // dd($variables);
        $distances = [];

        foreach ($variables as $variable) {

            $variableString = json_encode($variable->parameters);
            // dd($variableString);
            $variableData = json_decode($variableString, true);
            $distance = 0;

            foreach ($normalizedData as $key => $value) {
                // dd($variableData);
                if (isset($variableData[$key])) {
                    if (is_array($value)) {
                        for ($i = 0; $i < count($value); $i++) {
                            $distance += pow($variableData[$key][$i] - $value[$i], 2);
                        }
                    } else {
                        $distance += pow($variableData[$key] - $value, 2);
                    }
                } else {
                    // $distance += pow(0 - $value, 2); // Assume missing data as 0
                    $distance += is_array($value) ? array_sum(array_map(fn($v) => pow($v, 2), $value)) : pow($value, 2);
                }
            }

            $distances[] = [
                'variable' => $variable,
                'distance' => sqrt($distance),
            ];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $recommendations = array_slice($distances, 0, 3);
        return array_map(function ($rec) {
            return $rec['variable']->toArray();
        }, $recommendations);
        // dd($recommendations);
    }

    // public function save(array $options = [])
    // {
    //     DB::transaction(function () {
    //         $normalizedData = static::normalizeData($this->input_data);
    //         $recommendations = static::calculateKNN($normalizedData);

    //         $this->update([
    //             'normalized_data' => $normalizedData,
    //             'recommendations' => $recommendations,
    //         ]);
    //     });
    // }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimulations::route('/'),
            'create' => Pages\CreateSimulation::route('/create'),
            'edit' => Pages\EditSimulation::route('/{record}/edit'),
        ];
    }
}
