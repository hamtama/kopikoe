<div class="container py-5">
    <!-- Header Section -->
    <div class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <span class="badge bg-success text-white px-3 py-2 rounded-pill font-semibold uppercase tracking-wider mb-2">
                    🌱 Portal Penggiat & Pakar Kopi
                </span>
                <h1 class="display-6 font-bold text-gray-800">Manajemen Sampel Kopi Kebun & Roastery</h1>
                <p class="text-muted">
                    Daftarkan profil cupping kopi milik Anda agar dapat ditemukan dan direkomendasikan secara cerdas kepada para pencari dan penikmat kopi.
                </p>
            </div>
            <div>
                <div class="btn-group shadow-sm rounded-pill p-1 bg-light">
                    <button type="button" wire:click="setTab('form')" 
                            class="btn px-4 py-2 rounded-pill font-bold {{ $activeTab === 'form' ? 'btn-success text-white' : 'btn-light text-muted' }}">
                        <i class="fi-rs-plus mr-1"></i> Daftarkan Kopi Baru
                    </button>
                    <button type="button" wire:click="setTab('list')" 
                            class="btn px-4 py-2 rounded-pill font-bold {{ $activeTab === 'list' ? 'btn-success text-white' : 'btn-light text-muted' }}">
                        <i class="fi-rs-list mr-1"></i> Kopi Milik Saya ({{ count($myCoffees) }})
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 p-4 mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fi-rs-check text-2xl mr-3"></i>
                <div>
                    <h6 class="font-bold mb-1">Berhasil!</h6>
                    <p class="mb-0 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 1: FORM DAFTARKAN KOPI BARU -->
    @if ($activeTab === 'form')
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-5">
            <div class="card-header bg-dark text-white p-4">
                <h5 class="mb-0 font-bold"><i class="fi-rs-document mr-2 text-warning"></i> Formulir Cupping & Pendaftaran Sampel Kopi</h5>
                <small class="text-gray-400">Isi data identitas kopi dan skor pengujian rasa (cupping) di bawah ini</small>
            </div>
            <div class="card-body p-4 p-md-5">
                <form wire:submit.prevent="saveCoffee">
                    <!-- 1. Identitas Kopi & Kebun -->
                    <h5 class="font-bold text-gray-800 border-bottom pb-2 mb-4">
                        <span class="text-warning mr-1">1.</span> Informasi Dasar Kopi & Kebun
                    </h5>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-bold text-gray-700">Nama Kopi <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="name" class="form-control rounded-3 py-2" placeholder="Contoh: Arabika Gayo Natural Process">
                            @error('name') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-bold text-gray-700">Jenis Kopi <span class="text-danger">*</span></label>
                            <select wire:model.defer="jenis_kopi" class="form-select rounded-3 py-2">
                                <option value="">--- Pilih Jenis Kopi ---</option>
                                @foreach($listJenisKopi as $jk)
                                    <option value="{{ $jk->id_jenis_kopi }}">{{ $jk->jenis_kopi }}</option>
                                @endforeach
                            </select>
                            @error('jenis_kopi') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-bold text-gray-700">Nama Petani / Roastery / Pemilik <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="nama_supplier" class="form-control rounded-3 py-2" placeholder="Nama brand atau kebun Anda">
                            @error('nama_supplier') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-bold text-gray-700">Lokasi / Alamat Kebun <span class="text-danger">*</span></label>
                            <input type="text" wire:model.defer="alamat" class="form-control rounded-3 py-2" placeholder="Contoh: Takengon, Aceh Tengah">
                            @error('alamat') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label font-bold text-gray-700">Foto Sampel / Kemasan Kopi (Opsional)</label>
                            <input type="file" wire:model="image" class="form-control rounded-3">
                            <small class="text-muted">Format: JPG, PNG, WEBP (Maksimal 2MB)</small>
                            <div wire:loading wire:target="image" class="text-xs text-primary mt-1">Mengupload gambar...</div>
                            @if ($image)
                                <div class="mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" class="rounded-3 shadow-sm" style="max-height: 120px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Parameter Cupping & Skor Karakteristik -->
                    <h5 class="font-bold text-gray-800 border-bottom pb-2 mb-4 mt-5">
                        <span class="text-warning mr-1">2.</span> Penilaian Karakteristik & Cupping Score (Standar SCA)
                    </h5>

                    <div class="row g-4 mb-4">
                        @foreach ($parameters as $parameter)
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 h-100 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label font-bold text-gray-800 mb-0">
                                            {{ $parameter->name }} ({{ $parameter->kode }})
                                        </label>
                                        @if(!$parameter->is_multiple)
                                            <span class="badge bg-success rounded-pill px-2 py-1">
                                                Skor: {{ $inputs[$parameter->kode] ?? ($parameter->min ?? 1) }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($parameter->is_multiple)
                                        <small class="text-muted d-block mb-2">Centang profil aroma / rasa yang dimiliki:</small>
                                        <div class="space-y-1">
                                            @foreach ($parameter->subParameters as $sub)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $sub->subparameter_id }}"
                                                        wire:model.defer="inputs.{{ $parameter->kode }}"
                                                        id="pg_sub_{{ $sub->subparameter_id }}">
                                                    <label class="form-check-label text-sm text-gray-700" for="pg_sub_{{ $sub->subparameter_id }}">
                                                        {{ $sub->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center gap-3 mt-3">
                                            <span class="text-xs text-muted">Min ({{ $parameter->min ?? 1 }})</span>
                                            <input type="range" class="form-range flex-grow-1"
                                                min="{{ $parameter->min ?? 1 }}"
                                                max="{{ $parameter->max ?? 15 }}"
                                                step="1"
                                                wire:model="inputs.{{ $parameter->kode }}">
                                            <span class="text-xs text-muted">Max ({{ $parameter->max ?? 15 }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tombol Submit -->
                    <div class="mt-5 text-end">
                        <button type="submit" class="btn btn-success text-white font-bold px-5 py-3 rounded-pill shadow hover:shadow-lg transition">
                            <span wire:loading.remove wire:target="saveCoffee">
                                💾 Simpan & Terbitkan Kopi ke Katalog Rekomendasi
                            </span>
                            <span wire:loading wire:target="saveCoffee">
                                <span class="spinner-border spinner-border-sm mr-2" role="status"></span> Menyimpan data...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- TAB 2: DAFTAR KOPI MILIK SAYA -->
    @if ($activeTab === 'list')
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-5">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-bold text-gray-800">
                    <i class="fi-rs-coffee mr-2 text-success"></i> Katalog Kopi yang Sudah Anda Daftarkan
                </h5>
                <button type="button" wire:click="setTab('form')" class="btn btn-sm btn-success rounded-pill px-3">
                    + Daftarkan Kopi Lainnya
                </button>
            </div>
            <div class="card-body p-0">
                @if($myCoffees->isEmpty())
                    <div class="text-center py-5">
                        <i class="fi-rs-box-alt text-5xl text-gray-300 d-block mb-3"></i>
                        <h6 class="text-gray-700 font-bold">Anda belum mendaftarkan kopi apapun</h6>
                        <p class="text-muted text-sm max-w-sm mx-auto mb-4">
                            Klik tombol di bawah ini untuk mulai menambahkan data profil cupping kopi milik kebun Anda.
                        </p>
                        <button type="button" wire:click="setTab('form')" class="btn btn-success px-4 rounded-pill">
                            Daftarkan Kopi Sekarang
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Kopi</th>
                                    <th>Jenis</th>
                                    <th>Lokasi / Kebun</th>
                                    <th>Status Rekomendasi</th>
                                    <th>Tanggal Input</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myCoffees as $coffee)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                @if(!empty($coffee->image) && isset($coffee->image[0]))
                                                    <img src="{{ asset('storage/' . $coffee->image[0]) }}" 
                                                         alt="{{ $coffee->name }}" 
                                                         class="rounded-3 mr-3 object-cover shadow-sm"
                                                         style="width: 50px; height: 50px;">
                                                @else
                                                    <div class="rounded-3 bg-light mr-3 d-flex align-items-center justify-content-center text-muted"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fi-rs-coffee"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0 font-bold text-gray-900">{{ $coffee->name }}</h6>
                                                    <small class="text-muted">{{ $coffee->nama_supplier }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill">
                                                {{ $coffee->JenisKopi->jenis_kopi ?? 'Kopi' }}
                                            </span>
                                        </td>
                                        <td class="text-sm text-gray-600">{{ $coffee->alamat ?? '-' }}</td>
                                        <td>
                                            @if($coffee->status == 1)
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">
                                                    ● Aktif Direkomendasikan
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1 rounded-pill">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-muted">
                                            {{ $coffee->created_at ? $coffee->created_at->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <button type="button" 
                                                    onclick="confirm('Yakin ingin menghapus kopi ini dari sistem?') || event.stopImmediatePropagation()"
                                                    wire:click="deleteCoffee({{ $coffee->variable_id }})" 
                                                    class="btn btn-sm btn-outline-danger rounded-circle p-2" 
                                                    title="Hapus Kopi">
                                                <i class="fi-rs-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
