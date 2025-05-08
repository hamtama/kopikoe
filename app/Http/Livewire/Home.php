<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\JenisKopi;
use App\Models\Variables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class Home extends Component
{
    use WithPagination;

    private function getProducts($sort, $selectedCategory, $search, $priceFilter)
    {
        // $query = Variables::query();
        $query = Variables::join('jenis_kopi', 'variables.jenis_kopi', '=', 'jenis_kopi.id_jenis_kopi')
            ->select('variables.*', 'jenis_kopi.jenis_kopi as nama_jeniskopi');
        // $query_jenis_kopi = JenisKopi::query();
        if ($selectedCategory) {
            $query->whereHas('jeniskopi', function ($query) use ($selectedCategory) {
                $query->where('jenis_kopi.jenis_kopi', $selectedCategory);
            });
        }

        if ($priceFilter) {
            preg_match('/\$(\d+)\s+-\s+\$(\d+)/', $priceFilter, $matches);

            if (count($matches) == 3) {
                $minPrice = $matches[1];
                $maxPrice = $matches[2];
            }
        }

        if (isset($minPrice) && isset($maxPrice)) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alamat', 'like', '%' . $search . '%')
                    ->orWhereHas('jeniskopi', function ($query) use ($search) {
                        $query->where('jenis_kopi.jenis_kopi', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($sort === 'latest') {
            $query->orderBy('created_at', 'DESC');
        } elseif ($sort === 'low-to-high') {
            $query->orderBy('price', 'ASC');
        } elseif ($sort === 'high-to-low') {
            $query->orderBy('price', 'DESC');
        }

        // Fetch the paginated products and append the sort and category parameters
        return $query->paginate(12)->appends(['sort' => $sort, 'category' => $selectedCategory, 'range' => $priceFilter, 'search' => $search]);
    }


    public function render(Request $request)
    {
        // Periksa apakah pengguna sudah login
        if (!Auth::check()) {
            // Jika pengguna belum login, jalankan logika berikut
            $sort = $request->input('sort');
            $selectedCategory = $request->input('category');
            $search = $request->input('search');
            $priceFilter = $request->input('range');
            $products = $this->getProducts($sort, $selectedCategory, $search, $priceFilter);
            $jeniskopi = JenisKopi::all();

            return view('livewire.home', [
                'products' => $products,
                'sort' => $sort,
                'selectedCategory' => $selectedCategory,
                'jeniskopi' => $jeniskopi
            ]);
        }

        // Jika pengguna sudah login, ambil data pengguna yang sedang login
        $user = Auth::user();

        // Periksa apakah profil pengguna sudah lengkap
        if (!$user->isProfileComplete()) {
            // Menyimpan pesan error ke session
            session()->flash('notif_profile', 'Lengkapi Terlebih Dahulu Data Profile Anda. <a href="' . route('profile.edit') . '" class="text-blue-500 underline"><b>Lihat Profile</b></a>');

            // Redirect pengguna ke halaman edit profil
            // return redirect()->route('profile.edit');
        }

        // Logika untuk mengambil produk dan kategori jika pengguna sudah login
        $sort = $request->input('sort');
        $selectedCategory = $request->input('category');
        $search = $request->input('search');
        $priceFilter = $request->input('range');
        $products = $this->getProducts($sort, $selectedCategory, $search, $priceFilter);
        $jeniskopi = JenisKopi::all();

        return view('livewire.home', [
            'products' => $products,
            'sort' => $sort,
            'selectedCategory' => $selectedCategory,
            'jeniskopi' => $jeniskopi
        ]);
    }
}
