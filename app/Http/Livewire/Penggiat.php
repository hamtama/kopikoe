<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Parameters;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use App\Models\UserCategory;
use Filament\Forms;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\CheckboxList;

class Penggiat extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $inputs = []; // Array untuk menampung nilai input dari form

    /**
     * Inisialisasi input default saat komponen dimuat pertama kali.
     */
    public function mount()
    {
        // Kosongkan array input saat mount pertama kali
        $this->inputs = [];
    }

    /**
     * Render komponen dan ambil data parameter secara realtime dari database
     */
    public function render()
    {
        Log::info('Rendering penggiat component');
        // Ambil semua data parameter beserta subParameters dari database setiap kali komponen dirender
        $parameters = Parameters::with('subParameters')->get();

        $parameters = json_decode(json_encode($parameters->toArray()));

        // Inisialisasi input jika belum ada
        foreach ($parameters as $parameter) {
            if (!isset($this->inputs[$parameter->kode])) {
                $this->inputs[$parameter->kode] = $parameter->is_multiple ? [] : null;
            }
        }

        return view('livewire.penggiat', [
            'parameters' => $parameters,
        ]);
    }

    /**
     * Mendefinisikan skema form berdasarkan parameter yang diambil
     */
    protected function getFormSchema(): array
    {
        $parameters = Parameters::with('subParameters')->get(); // Ambil data dari model di schema form

        $parameterInputs = [];

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;
            $displayLabel = $parameter->name;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('name', 'subparameter_id')->toArray();

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

        return $parameterInputs;
    }

    /**
     * Method untuk menangani submit form
     */
    public function submit()
    {
        // Debug data yang di-submit
        dd($this->inputs);
    }
}
