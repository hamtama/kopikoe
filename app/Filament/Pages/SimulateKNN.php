<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Page;
use App\Models\Parameters;
use App\Models\Simulation;
use App\Models\Variables;
use App\Models\SubParameter;
use App\Models\JenisKopi;
use App\Forms\Components\LevelInput;

class SimulateKNN extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static string $view = 'filament.pages.simulate-knn';
    protected static ?string $slug = 'simulation-knn';
    protected static ?string $title = 'Simulasi Perhitungan';

    public $parameters;
    public $inputData = [];
    public $normalizedData = [];
    public $recommendations = [];

    public function mount()
    {
        $this->parameters = Parameters::with('subParameters')->get();
        $this->form->fill([
            'inputData' => [],
        ]);
    }

    protected function getFormSchema(): array
    {
        $parameterInputs = [];

        foreach ($this->parameters as $parameter) {
            $transformedName = $parameter->kode;
            $displayLabel = $parameter->name;
            // dd($displayLabel);
            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                // $parameterInputs[] = Forms\Components\CheckboxList::make("inputData.{$transformedName}")
                //     ->label($parameter->name)
                //     ->options($subParameters)
                //     ->extraAttributes(['class' => 'grid grid-cols-1 md:grid-cols-5 gap-4 mb-4'])
                //     ->prefixLabel() // Agar label di sebelah kiri
                //     ->inline(false) // Agar checkbox ditampilkan secara vertikal
                //     ->labelWrapperAttributes(['class' => 'flex flex-col justify-center items-start']) // Custom label position
                //     ->wrapperAttributes(['class' => 'col-span-2 flex flex-col justify-center']); // Custom checkbox position
                $parameterInputs[] = Grid::make(5)
                    ->schema([
                        Placeholder::make("label_{$transformedName}")
                            ->content($displayLabel)
                            ->label(false)
                            ->extraAttributes(['class' => 'text-sm text-gray-700']),
                        CheckboxList::make("inputData.{$transformedName}")
                            ->options($subParameters)
                            ->columns(1) // Menampilkan checkbox dalam satu kolom
                            ->columnSpan(2)
                            ->label(false)
                            // ->columnSpace(1)
                            ->extraAttributes(['class' => 'space-y-1 flex flex-col justify-center']),
                    ]);
                // ->columns(5);
            } else {

                $parameterInputs[] = Forms\Components\View::make('components.level-input')
                    ->label($displayLabel) // Menggunakan nama parameter sebagai label
                    ->statePath("inputData.{$transformedName}")
                    ->viewData([
                        'name' => "inputData.{$transformedName}",
                        'id' => $transformedName,
                        'displayLabel' => $displayLabel,
                        'labels' => ['Low', 'Medium', 'High'], // Sesuaikan label sesuai kebutuhan
                        'numberRange' => [$parameter->min, $parameter->max], // Rentang nilai parameter
                        'disabled' => false,
                    ]);
            }
        }

        return array_merge([
            Forms\Components\TextInput::make('inputData.name')->required(),
            Select::make('inputData.jenis_kopi')
                ->label('Jenis Kopi')
                ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi'))
        ], $parameterInputs);
    }



    public function normalize()
    {
        // dd($this->form->getState());
        $this->inputData = $this->form->getState()['inputData'];
        $this->normalizedData = $this->normalizeData($this->inputData);
    }

    public function calculateKNN()
    {
        $this->recommendations = $this->calculateKNNRecommendations($this->normalizedData);
        Simulation::create([
            'input_data' => json_encode($this->inputData),
            'normalized_data' => json_encode($this->normalizedData),
            'recommendations' => json_encode($this->recommendations),
        ]);
    }

    private function normalizeData($inputData)
    {
        // dd($inputData);
        $normalized = [];
        $normalized['jenis_kopi'] = $inputData['jenis_kopi'];
        $parameters = Parameters::with('subParameters')->get();

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if (isset($inputData[$transformedName])) {
                $value = $inputData[$transformedName];

                if ($parameter->is_multiple) {
                    $subParameters = SubParameter::where('parameter_id', $parameter->parameter_id)->pluck('subparameter_id')->toArray();
                    foreach ($subParameters as $subParameter) {
                        $normalizedValue = in_array($subParameter, $value) ? 1 : 0;
                        $normalized["{$transformedName}_{$subParameter}"] = $normalizedValue;
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




    private function calculateKNNRecommendations($normalizedData)
    {
        $jenis_kopi = $normalizedData['jenis_kopi'];
        $variables = Variables::where('jenis_kopi', $jenis_kopi)->get();
        $distances = [];

        foreach ($variables as $variable) {
            $variableData = $variable->normalisasi;
            $distance = 0;

            foreach ($normalizedData as $key => $value) {
                if (isset($variableData[$key])) {
                    if (is_array($value)) {
                        for ($i = 0; $i < count($value); $i++) {
                            $distance += pow($variableData[$key][$i] - $value[$i], 2);
                        }
                    } else {
                        $distance += pow($variableData[$key] - $value, 2);
                    }
                } else {
                    // $distance += pow(0 - $value, 2);
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
            $variableArray = $rec['variable']->toArray();
            $variableArray['distance'] = $rec['distance'];
            return $variableArray;
        }, $recommendations);
    }

    public static function getNavigationLabel(): string
    {
        return 'Simulasi KNN';
    }

    protected function getActions(): array
    {
        return [];
    }
}
