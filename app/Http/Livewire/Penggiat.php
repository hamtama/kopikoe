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

    // Form fields
    public $name = '';
    public $nama_supplier = '';
    public $alamat = '';
    public $jenis_kopi = '';
    public $image;
    public $inputs = [];

    // Tab state ('form' atau 'list')
    public $activeTab = 'form';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'required|string|max:255',
        'jenis_kopi' => 'required',
        'image' => 'nullable|image|max:2048', // Maksimal 2MB
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
    }

    public function saveCoffee()
    {
        $this->validate();

        // Upload gambar jika ada
        $imagePath = [];
        if ($this->image) {
            $path = $this->image->store('variables', 'public');
            $imagePath = [$path];
        }

        // Hitung normalisasi data kriteria secara otomatis
        $normalizedData = Variables::normalizeData($this->inputs);

        // Simpan ke tabel variables
        Variables::create([
            'name' => $this->name,
            'nama_supplier' => $this->nama_supplier,
            'alamat' => $this->alamat,
            'jenis_kopi' => $this->jenis_kopi,
            'image' => !empty($imagePath) ? $imagePath : null,
            'parameters' => $this->inputs,
            'normalisasi' => $normalizedData,
            'status' => 1, // Langsung aktif di sistem rekomendasi
        ]);

        session()->flash('success', 'Kopi berhasil didaftarkan! Kopi Anda kini sudah masuk dalam sistem katalog dan siap direkomendasikan kepada pencari kopi.');

        // Reset form
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
            session()->flash('success', 'Data kopi berhasil dihapus dari sistem.');
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $parameters = Parameters::with('subParameters')->get();
        $listJenisKopi = JenisKopi::all();

        // Ambil daftar kopi yang didaftarkan oleh supplier ini
        $myCoffees = Variables::with('JenisKopi')
            ->where('nama_supplier', $this->nama_supplier)
            ->orWhere('nama_supplier', Auth::user()->name)
            ->latest()
            ->get();

        return view('livewire.penggiat', [
            'parameters' => $parameters,
            'listJenisKopi' => $listJenisKopi,
            'myCoffees' => $myCoffees,
        ])->layout('layouts.app');
    }
}
