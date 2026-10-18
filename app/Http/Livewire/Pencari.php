<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Parameters;
use App\Models\JenisKopi;
use App\Models\Variables;

class Pencari extends Component
{
    public $jenis_kopi_id = '';
    public $inputs = [];
    public $recommendations = [];
    public $hasSearched = false;

    public function mount()
    {
        $parameters = Parameters::with('subParameters')->get();
        foreach ($parameters as $param) {
            if ($param->is_multiple) {
                $this->inputs[$param->kode] = [];
            } else {
                $mid = (int) round((($param->min ?? 1) + ($param->max ?? 15)) / 2);
                $this->inputs[$param->kode] = $mid;
            }
        }
    }

    public function setJenisKopi($id)
    {
        $this->jenis_kopi_id = $id;
    }

    public function toggleSubParam($kode, $subId)
    {
        if (!isset($this->inputs[$kode]) || !is_array($this->inputs[$kode])) {
            $this->inputs[$kode] = [];
        }

        if (in_array($subId, $this->inputs[$kode])) {
            $this->inputs[$kode] = array_values(array_diff($this->inputs[$kode], [$subId]));
        } else {
            $this->inputs[$kode][] = $subId;
        }
    }

    public function calculateRecommendation()
    {
        $parameters = Parameters::with('subParameters')->get();

        $normalizedInput = [];
        foreach ($parameters as $parameter) {
            $key = $parameter->kode;
            $val = $this->inputs[$key] ?? null;

            if ($parameter->is_multiple) {
                $subParams = $parameter->subParameters->pluck('subparameter_id')->toArray();
                $selected = is_array($val) ? $val : [];
                foreach ($subParams as $subId) {
                    $normalizedInput["{$key}_{$subId}"] = in_array($subId, $selected) ? 1.0 : 0.0;
                }
            } else {
                $min = (float) ($parameter->min ?? 1);
                $max = (float) ($parameter->max ?? 15);
                $numVal = (float) ($val ?? $min);
                $range = ($max - $min) != 0 ? ($max - $min) : 1.0;

                if (strtolower($parameter->type) === 'benefit') {
                    $normalizedInput[$key] = round(($numVal - $min) / $range, 4);
                } else {
                    $normalizedInput[$key] = round(($max - $numVal) / $range, 4);
                }
            }
        }

        $query = Variables::with('JenisKopi')->where('status', 1);
        if (!empty($this->jenis_kopi_id)) {
            $query->where('jenis_kopi', $this->jenis_kopi_id);
        }

        $dataset = $query->get();

        if ($dataset->isEmpty()) {
            $this->recommendations = [];
            $this->hasSearched = true;
            return;
        }

        $distances = [];
        foreach ($dataset as $coffee) {
            $coffeeNorm = is_array($coffee->normalisasi) 
                ? $coffee->normalisasi 
                : json_decode($coffee->normalisasi, true) ?? [];

            $sumSquaredDiff = 0.0;
            foreach ($normalizedInput as $featureKey => $inputValue) {
                $coffeeValue = isset($coffeeNorm[$featureKey]) ? (float) $coffeeNorm[$featureKey] : 0.0;
                $sumSquaredDiff += pow($inputValue - $coffeeValue, 2);
            }

            $distance = sqrt($sumSquaredDiff);
            $similarity = round((1.0 / (1.0 + $distance)) * 100, 1);

            $distances[] = [
                'id' => $coffee->variable_id,
                'name' => $coffee->name,
                'nama_supplier' => $coffee->nama_supplier ?? 'Pakar Kopi',
                'alamat' => $coffee->alamat ?? '-',
                'jenis_kopi' => $coffee->JenisKopi->jenis_kopi ?? 'Kopi Spesial',
                'image' => is_array($coffee->image) ? ($coffee->image[0] ?? null) : $coffee->image,
                'distance' => round($distance, 4),
                'similarity' => $similarity,
            ];
        }

        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $this->recommendations = array_slice($distances, 0, 4);
        $this->hasSearched = true;

        $this->dispatchBrowserEvent('scroll-to-results');
    }

    public function resetSearch()
    {
        $this->mount();
        $this->recommendations = [];
        $this->hasSearched = false;
        $this->jenis_kopi_id = '';
    }

    public function render()
    {
        return view('livewire.pencari', [
            'parameters' => Parameters::with('subParameters')->get(),
            'listJenisKopi' => JenisKopi::all(),
        ]);
    }
}