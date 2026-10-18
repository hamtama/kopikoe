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
use Filament\Notifications\Notification;

class SimulateKNN extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static string $view = 'filament.pages.simulate-knn';
    protected static ?string $slug = 'simulation-knn';
    protected static ?string $title = 'Simulasi Perhitungan KNN';
    protected static ?string $navigationGroup = 'Data Testing Collection';
    protected static ?int $navigationSort = 1;

    public $parameters;
    public $inputData = [];
    public $normalizedData = [];
    public $recommendations = [];

    public function mount()
    {
        $this->parameters = Parameters::with('subParameters')->get();
        $this->form->fill([
            'inputData' => [
                'name' => 'Skenario Uji ' . date('d/m/Y H:i'),
                'jenis_kopi' => '',
            ],
        ]);
    }

    protected function getFormSchema(): array
    {
        $parameterInputs = [];

        foreach ($this->parameters as $parameter) {
            $transformedName = $parameter->kode;
            $displayLabel = $parameter->name . ' (' . $parameter->kode . ')';

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();
                $parameterInputs[] = Forms\Components\Card::make([
                    Placeholder::make("label_{$transformedName}")
                        ->content($displayLabel . ' - Tipe: ' . ucfirst($parameter->type))
                        ->label(false),
                    CheckboxList::make("inputData.{$transformedName}")
                        ->label('Pilih profil karakteristik:')
                        ->options($subParameters)
                        ->columns(2),
                ])->columnSpan(1);
            } else {
                $parameterInputs[] = Forms\Components\Card::make([
                    Forms\Components\TextInput::make("inputData.{$transformedName}")
                        ->label($displayLabel)
                        ->helperText("Skala: {$parameter->min} - {$parameter->max} | Tipe: " . ucfirst($parameter->type))
                        ->numeric()
                        ->minValue($parameter->min ?? 1)
                        ->maxValue($parameter->max ?? 15)
                        ->default((int) round((($parameter->min ?? 1) + ($parameter->max ?? 15)) / 2))
                        ->required(),
                ])->columnSpan(1);
            }
        }

        return [
            Forms\Components\Section::make('Identitas Skenario Pengujian')
                ->description('Tentukan nama pengujian dan filter jenis kopi')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('inputData.name')
                            ->label('Nama Skenario / Penguji')
                            ->placeholder('Contoh: Pengujian Sampel Robusta')
                            ->required(),
                        Select::make('inputData.jenis_kopi')
                            ->label('Jenis Kopi (Filter)')
                            ->options(JenisKopi::all()->pluck('jenis_kopi', 'id_jenis_kopi'))
                            ->placeholder('Semua Jenis Kopi'),
                    ]),
                ]),

            Forms\Components\Section::make('Nilai Parameter Kriteria Kopi')
                ->description('Isi nilai kuantitatif karakteristik kopi yang diuji')
                ->schema([
                    Grid::make(2)->schema($parameterInputs),
                ]),
        ];
    }

    // Jalankan Simulasi Sekaligus (1-Click)
    public function runFullSimulation()
    {
        $this->normalize();
        $this->calculateKNN();

        Notification::make()
            ->title('Simulasi KNN Berhasil Dijalankan')
            ->body('Data berhasil dinormalisasi dan dihitung jarak Euclidean terdekatnya.')
            ->success()
            ->send();
    }

    public function normalize()
    {
        $state = $this->form->getState();
        $this->inputData = $state['inputData'];
        $this->normalizedData = $this->normalizeData($this->inputData);
    }

    public function calculateKNN()
    {
        if (empty($this->normalizedData)) {
            $this->normalize();
        }

        $this->recommendations = $this->calculateKNNRecommendations($this->normalizedData);

        // Simpan log riwayat simulasi ke database
        Simulation::create([
            'input_data' => json_encode($this->inputData),
            'normalized_data' => json_encode($this->normalizedData),
            'recommendations' => json_encode($this->recommendations),
        ]);
    }

    private function normalizeData($inputData)
    {
        $normalized = [];
        $normalized['jenis_kopi'] = $inputData['jenis_kopi'] ?? null;
        $parameters = Parameters::with('subParameters')->get();

        foreach ($parameters as $parameter) {
            $key = $parameter->kode;
            $value = $inputData[$key] ?? null;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('subparameter_id')->toArray();
                $selected = is_array($value) ? $value : [];
                foreach ($subParameters as $subParameter) {
                    $normalized["{$key}_{$subParameter}"] = in_array($subParameter, $selected) ? 1.0 : 0.0;
                }
            } else {
                $min = (float) ($parameter->min ?? 1);
                $max = (float) ($parameter->max ?? 15);
                $numVal = (float) ($value ?? $min);
                $range = ($max - $min) != 0 ? ($max - $min) : 1.0;

                if (strtolower($parameter->type) === 'benefit') {
                    $normalized[$key] = round(($numVal - $min) / $range, 4);
                } else {
                    $normalized[$key] = round(($max - $numVal) / $range, 4);
                }
            }
        }

        return $normalized;
    }

    private function calculateKNNRecommendations($normalizedData)
    {
        $query = Variables::with('JenisKopi')->where('status', 1);

        if (!empty($normalizedData['jenis_kopi'])) {
            $query->where('jenis_kopi', $normalizedData['jenis_kopi']);
        }

        $variables = $query->get();
        $distances = [];

        foreach ($variables as $variable) {
            $variableData = is_array($variable->normalisasi) 
                ? $variable->normalisasi 
                : json_decode($variable->normalisasi, true) ?? [];

            $distance = 0.0;

            foreach ($normalizedData as $key => $value) {
                if ($key === 'jenis_kopi') continue;

                $targetVal = isset($variableData[$key]) ? (float) $variableData[$key] : 0.0;
                $inputVal = (float) $value;
                $distance += pow($targetVal - $inputVal, 2);
            }

            $distSqrt = sqrt($distance);
            $similarity = round((1.0 / (1.0 + $distSqrt)) * 100, 1);

            $distances[] = [
                'variable_id' => $variable->variable_id,
                'name' => $variable->name,
                'nama_supplier' => $variable->nama_supplier ?? 'Pakar Kopi',
                'alamat' => $variable->alamat ?? '-',
                'jenis_kopi' => $variable->JenisKopi->jenis_kopi ?? 'Kopi',
                'image' => is_array($variable->image) ? ($variable->image[0] ?? null) : $variable->image,
                'distance' => round($distSqrt, 4),
                'similarity' => $similarity,
                'parameters' => is_array($variable->parameters) ? $variable->parameters : json_decode($variable->parameters, true),
            ];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return array_slice($distances, 0, 5);
    }

    public static function getNavigationLabel(): string
    {
        return 'Simulasi KNN';
    }
}
