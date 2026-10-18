<div class="container py-5">
    <!-- Header Section -->
    <div class="mb-5 text-center">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill font-semibold uppercase tracking-wider mb-2">
            ☕ Rekomendasi Kopi Cerdas (KNN)
        </span>
        <h1 class="display-6 font-bold text-gray-800">Cari Kopi Sesuai Karakter Seleramu</h1>
        <p class="text-muted max-w-2xl mx-auto">
            Atur tingkat intensitas rasa dan aroma yang Anda inginkan. Algoritma K-Nearest Neighbors (KNN) akan mencocokkan preferensi Anda dengan seluruh data kopi dari para pakar & petani kopi.
        </p>
    </div>

    <div class="row">
        <!-- Form Kriteria Preferensi (Sebelah Kiri) -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h5 class="mb-0 font-bold"><i class="fi-rs-filter mr-2"></i> Form Preferensi Rasa</h5>
                    <small class="text-gray-400">Sesuaikan slider/pilihan di bawah ini</small>
                </div>
                <div class="card-body p-4 bg-white">
                    <form wire:submit.prevent="calculateRecommendation">
                        <!-- Jenis Kopi -->
                        <div class="mb-4">
                            <label class="form-label font-bold text-gray-700">Pilihan Jenis Kopi</label>
                            <select wire:model.defer="jenis_kopi_id" class="form-select rounded-3 py-2">
                                <option value="">Semua Jenis Kopi (Arabica & Robusta)</option>
                                @foreach($listJenisKopi as $jk)
                                    <option value="{{ $jk->id_jenis_kopi }}">{{ $jk->jenis_kopi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4">

                        <!-- Parameter Dinamis dari Database -->
                        @foreach ($parameters as $parameter)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label font-bold text-gray-800 mb-0">
                                        {{ $parameter->name }}
                                    </label>
                                    @if(!$parameter->is_multiple)
                                        <span class="badge bg-primary rounded-pill px-2 py-1">
                                            Nilai: {{ $inputs[$parameter->kode] ?? ($parameter->min ?? 1) }}
                                        </span>
                                    @endif
                                </div>

                                @if ($parameter->is_multiple)
                                    <small class="text-muted d-block mb-2">Pilih karakteristik rasa yang Anda sukai:</small>
                                    <div class="p-3 bg-light rounded-3">
                                        @foreach ($parameter->subParameters as $sub)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $sub->subparameter_id }}"
                                                    wire:model.defer="inputs.{{ $parameter->kode }}"
                                                    id="sub_{{ $sub->subparameter_id }}">
                                                <label class="form-check-label text-sm text-gray-700" for="sub_{{ $sub->subparameter_id }}">
                                                    {{ $sub->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="text-xs text-muted">Low ({{ $parameter->min ?? 1 }})</span>
                                        <input type="range" class="form-range flex-grow-1"
                                            min="{{ $parameter->min ?? 1 }}"
                                            max="{{ $parameter->max ?? 15 }}"
                                            step="1"
                                            wire:model="inputs.{{ $parameter->kode }}">
                                        <span class="text-xs text-muted">High ({{ $parameter->max ?? 15 }})</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <!-- Tombol Aksi -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning text-white font-bold py-3 rounded-3 shadow-sm hover:shadow-md transition">
                                <span wire:loading.remove wire:target="calculateRecommendation">
                                    🔍 Cari Rekomendasi Kopi Sekarang
                                </span>
                                <span wire:loading wire:target="calculateRecommendation">
                                    <span class="spinner-border spinner-border-sm mr-2" role="status"></span> Menghitung Kecocokan...
                                </span>
                            </button>
                            <button type="button" wire:click="resetSearch" class="btn btn-outline-secondary py-2 rounded-3">
                                Reset Preferensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hasil Rekomendasi (Sebelah Kanan) -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 font-bold text-gray-800">
                        <i class="fi-rs-cup mr-2 text-warning"></i> Hasil Rekomendasi Kopi
                    </h4>
                    @if($hasSearched)
                        <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                            Ditemukan {{ count($recommendations) }} Kopi Terbaik
                        </span>
                    @endif
                </div>

                @if(!$hasSearched)
                    <div class="text-center py-5 my-auto">
                        <div class="mb-4">
                            <i class="fi-rs-coffee text-6xl text-gray-300"></i>
                        </div>
                        <h5 class="text-gray-700 font-bold">Belum Ada Rekomendasi yang Ditampilkan</h5>
                        <p class="text-muted max-w-md mx-auto">
                            Silakan atur kriteria rasa kopi favorit Anda pada form di sebelah kiri, kemudian klik tombol <b>Cari Rekomendasi Kopi Sekarang</b>.
                        </p>
                    </div>
                @elseif(empty($recommendations))
                    <div class="alert alert-warning rounded-3 p-4 text-center my-auto">
                        <i class="fi-rs-info text-3xl mb-2 d-block"></i>
                        <h5 class="font-bold">Tidak Ada Kopi yang Ditemukan</h5>
                        <p class="mb-0 text-sm">Belum ada data sampel kopi di sistem atau kriteria filter jenis kopi terlalu spesifik. Coba pilih 'Semua Jenis Kopi' atau minta Penggiat Kopi mendaftarkan kopi baru.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($recommendations as $index => $rec)
                            <div class="card border rounded-3 p-3 mb-3 shadow-hover transition {{ $index === 0 ? 'border-warning bg-amber-50/30' : 'border-gray-200' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-3 mb-md-0">
                                        @if(!empty($rec['image']))
                                            <img src="{{ asset('storage/' . $rec['image']) }}" 
                                                 alt="{{ $rec['name'] }}" 
                                                 class="rounded-3 img-fluid object-cover shadow-sm"
                                                 style="max-height: 120px; width: 100%;">
                                        @else
                                            <div class="bg-gray-100 rounded-3 d-flex align-items-center justify-content-center" style="height: 100px;">
                                                <i class="fi-rs-coffee text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                @if($index === 0)
                                                    <span class="badge bg-warning text-dark font-bold px-2 py-1 mb-1">
                                                        🏆 Ranking 1 (Paling Cocok)
                                                    </span>
                                                @elseif($index === 1)
                                                    <span class="badge bg-secondary text-white px-2 py-1 mb-1">
                                                        🥈 Ranking 2
                                                    </span>
                                                @elseif($index === 2)
                                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 mb-1">
                                                        🥉 Ranking 3
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark px-2 py-1 mb-1">
                                                        Pilihan #{{ $index + 1 }}
                                                    </span>
                                                @endif
                                                <h5 class="font-bold text-gray-900 mb-0">{{ $rec['name'] }}</h5>
                                            </div>
                                            <div class="text-end">
                                                <div class="text-2xl font-black text-warning leading-none">
                                                    {{ $rec['similarity'] }}%
                                                </div>
                                                <small class="text-muted text-xs">Tingkat Kemiripan</small>
                                            </div>
                                        </div>

                                        <!-- Progress Bar Kecocokan -->
                                        <div class="progress mb-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: {{ $rec['similarity'] }}%" 
                                                 aria-valuenow="{{ $rec['similarity'] }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap gap-3 text-xs text-muted mb-2">
                                            <span><i class="fi-rs-tag mr-1 text-primary"></i> <b>{{ $rec['jenis_kopi'] }}</b></span>
                                            <span><i class="fi-rs-home mr-1 text-success"></i> {{ $rec['nama_supplier'] }}</span>
                                            <span><i class="fi-rs-marker mr-1 text-danger"></i> {{ $rec['alamat'] }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                            <small class="text-muted">
                                                Jarak Euclidean: <code>{{ $rec['distance'] }}</code>
                                            </small>
                                            <a href="{{ route('product.details', $rec['id']) }}" class="btn btn-sm btn-outline-dark px-3 rounded-pill font-semibold">
                                                Lihat Profil Lengkap <i class="fi-rs-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
