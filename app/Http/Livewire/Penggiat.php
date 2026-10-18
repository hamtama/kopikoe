<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Parameters;
use App\Models\JenisKopi;
use App\Models\Variables;
use Illuminate\Support\Facades\Auth;

class Penggiat extends Component
{
    use WithFileUploads;

    public $name = '';
    public $nama_supplier = '';
    public $alamat = '';
    public $jenis_kopi = '';
    public $image;
    public $inputs = [];
    public $activeTab = 'list'; // Default membuka tab daftar kopi

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'required|string|max:255',
        'jenis_kopi' => 'required',
        'image' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->nama_supplier = $user->name ?? '';
        $this->alamat = $user->alamat ?? '';

        $parameters = Parameters::with('subParameters')->get();
        foreach ($parameters as $param) {
            if ($param->is_multiple) {
                $this->inputs[$param->kode] = [];
            } else {
                $mid = (int) round((($param->min ?? 1) + ($param->max ?? 15)) / 2);
                $this->inputs[$param->kode] = $mid;
            }
        }

        // Jika belum ada kopi sama sekali, arahkan langsung ke form
        $count = Variables::where('nama_supplier', $this->nama_supplier)
            ->orWhere('nama_supplier', $user->name ?? '')
            ->count();
            
        if ($count === 0) {
            $this->activeTab = 'form';
        }
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

    public function saveCoffee()
    {
        $this->validate();

        $imagePath = [];
        if ($this->image) {
            $path = $this->image->store('variables', 'public');
            $imagePath = [$path];
        }

        $normalizedData = Variables::normalizeData($this->inputs);

        Variables::create([
            'name' => $this->name,
            'nama_supplier' => $this->nama_supplier,
            'alamat' => $this->alamat,
            'jenis_kopi' => $this->jenis_kopi,
            'image' => !empty($imagePath) ? $imagePath : null,
            'parameters' => $this->inputs,
            'normalisasi' => $normalizedData,
            'status' => 1,
        ]);

        session()->flash('success', 'Kopi berhasil didaftarkan dan sudah aktif di katalog rekomendasi!');

        $this->name = '';
        $this->image = null;
        $this->mount();
        $this->activeTab = 'list';
    }

    public function deleteCoffee($variableId)
    {
        $coffee = Variables::find($variableId);
        if ($coffee) {
            $coffee->delete();
            session()->flash('success', 'Kopi berhasil dihapus dari katalog.');
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $myCoffees = Variables::with('JenisKopi')
            ->where('nama_supplier', $this->nama_supplier)
            ->orWhere('nama_supplier', Auth::user()->name)
            ->latest()
            ->get();

        return view('livewire.penggiat', [
            'parameters' => Parameters::with('subParameters')->get(),
            'listJenisKopi' => JenisKopi::all(),
            'myCoffees' => $myCoffees,
        ]);
    }
}