<div style="background-color: #FAF8F5; min-height: 100vh; padding: 40px 0;">
    <div class="container" style="max-width: 1050px;">

        <!-- Header Minimalis & Elegan -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #ECE7E1;">
            <div>
                <span style="font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; color: #8C6D58; font-weight: 700; display: block; margin-bottom: 4px;">
                    Portal Penggiat & Petani Kopi
                </span>
                <h2 style="color: #2D2424; font-weight: 700; margin: 0; font-size: 1.8rem;">
                    Manajemen Sampel Kopi & Kebun
                </h2>
                <p style="color: #7A726D; font-size: 0.88rem; margin-top: 4px; margin-bottom: 0;">
                    Daftarkan profil cupping kopi milik Anda agar dapat direkomendasikan kepada para penikmat kopi.
                </p>
            </div>

            <!-- Tombol Tab Navigasi Kontras Jelas -->
            <div class="mt-3 mt-md-0">
                <div style="background: #FFFFFF; border: 1px solid #E0D8D0; border-radius: 35px; padding: 4px; display: inline-flex; gap: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                    <button type="button" wire:click="setTab('list')" 
                            style="border-radius: 30px; padding: 8px 22px; font-weight: 700; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.2s ease; background: {{ $activeTab === 'list' ? '#2D2424' : 'transparent' }}; color: {{ $activeTab === 'list' ? '#FFFFFF' : '#69584D' }};">
                        📋 Kopi Milik Saya ({{ count($myCoffees) }})
                    </button>
                    <button type="button" wire:click="setTab('form')" 
                            style="border-radius: 30px; padding: 8px 22px; font-weight: 700; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.2s ease; background: {{ $activeTab === 'form' ? '#2D2424' : 'transparent' }}; color: {{ $activeTab === 'form' ? '#FFFFFF' : '#69584D' }};">
                        + Daftarkan Kopi Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Notifikasi Sukses -->
        @if (session()->has('success'))
            <div style="background: #EEF8F1; border: 1px solid #C2E8CC; border-radius: 14px; padding: 14px 20px; color: #1B6634; font-size: 0.9rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                <div>✓ {{ session('success') }}</div>
            </div>
        @endif

        <!-- TAB 1: DAFTAR KOPI SAYA (LIST) -->
        @if ($activeTab === 'list')
            <div style="background: #FFFFFF; border-radius: 20px; border: 1px solid #ECE7E1; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <div style="padding: 24px 28px; border-bottom: 1px solid #F0ECE7; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="color: #2D2424; font-weight: 700; font-size: 1.15rem; margin: 0;">
                            Katalog Kopi yang Anda Kelola
                        </h4>
                        <small style="color: #8C827A;">Semua kopi di bawah ini sudah aktif di sistem pencarian cerdas.</small>
                    </div>
                    <button type="button" wire:click="setTab('form')" 
                            style="background: #2D2424; color: #FFFFFF; font-weight: 700; font-size: 0.82rem; border-radius: 25px; padding: 8px 20px; border: none; cursor: pointer;">
                        + Tambah Kopi
                    </button>
                </div>

                @if($myCoffees->isEmpty())
                    <div class="text-center py-5">
                        <i class="fi-rs-coffee" style="font-size: 3.5rem; color: #D6CCC2; display: block; margin-bottom: 12px;"></i>
                        <h5 style="color: #2D2424; font-weight: 700;">Belum Ada Kopi yang Didaftarkan</h5>
                        <p style="color: #8C827A; font-size: 0.88rem; max-width: 400px; margin: 0 auto 20px auto;">
                            Tambahkan sampel kopi pertama dari kebun atau roastery Anda agar bisa ditemukan oleh para pembeli.
                        </p>
                        <button type="button" wire:click="setTab('form')" 
                                style="background: #2D2424; color: #FFFFFF; font-weight: 700; font-size: 0.85rem; border-radius: 25px; padding: 10px 24px; border: none; cursor: pointer;">
                            Daftarkan Kopi Sekarang
                        </button>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="border-collapse: separate;">
                            <thead style="background: #FAF8F5;">
                                <tr>
                                    <th style="padding: 14px 28px; font-size: 0.75rem; text-transform: uppercase; color: #8C6D58; font-weight: 700;">Nama Kopi</th>
                                    <th style="padding: 14px 18px; font-size: 0.75rem; text-transform: uppercase; color: #8C6D58; font-weight: 700;">Jenis</th>
                                    <th style="padding: 14px 18px; font-size: 0.75rem; text-transform: uppercase; color: #8C6D58; font-weight: 700;">Asal / Kebun</th>
                                    <th style="padding: 14px 18px; font-size: 0.75rem; text-transform: uppercase; color: #8C6D58; font-weight: 700;">Status</th>
                                    <th style="padding: 14px 28px; font-size: 0.75rem; text-transform: uppercase; color: #8C6D58; font-weight: 700; text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myCoffees as $coffee)
                                    <tr style="border-bottom: 1px solid #F5F0EB;">
                                        <td style="padding: 16px 28px;">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 44px; height: 44px; border-radius: 10px; background: #F4EFEB; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-right: 14px; flex-shrink: 0;">
                                                    @if(!empty($coffee->image) && isset($coffee->image[0]))
                                                        <img src="{{ asset('storage/' . $coffee->image[0]) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    @else
                                                        <i class="fi-rs-coffee" style="color: #A89B91;"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div style="font-weight: 700; color: #2D2424; font-size: 0.95rem;">{{ $coffee->name }}</div>
                                                    <small style="color: #8C827A;">{{ $coffee->nama_supplier }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 16px 18px;">
                                            <span style="background: #F4EFEB; color: #69584D; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 12px;">
                                                {{ $coffee->JenisKopi->jenis_kopi ?? 'Kopi' }}
                                            </span>
                                        </td>
                                        <td style="padding: 16px 18px; color: #69584D; font-size: 0.85rem;">
                                            📍 {{ $coffee->alamat ?? '-' }}
                                        </td>
                                        <td style="padding: 16px 18px;">
                                            <span style="background: #EEF8F1; color: #1B6634; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                ● Aktif di Rekomendasi
                                            </span>
                                        </td>
                                        <td style="padding: 16px 28px; text-align: right;">
                                            <button type="button" 
                                                    onclick="confirm('Yakin ingin menghapus kopi ini?') || event.stopImmediatePropagation()"
                                                    wire:click="deleteCoffee({{ $coffee->variable_id }})" 
                                                    style="background: #FFF0F0; color: #D63031; border: 1px solid #FFD1D1; font-size: 0.78rem; font-weight: 700; border-radius: 20px; padding: 6px 14px; cursor: pointer;">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- TAB 2: FORM PENDAFTARAN KOPI BARU -->
        @if ($activeTab === 'form')
            <div style="background: #FFFFFF; border-radius: 24px; border: 1px solid #ECE7E1; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
                <form wire:submit.prevent="saveCoffee">
                    
                    <!-- 1. Identitas Kopi -->
                    <div class="mb-4 pb-4" style="border-bottom: 1px solid #F0ECE7;">
                        <span style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; color: #C67C4E; font-weight: 700; display: block; margin-bottom: 4px;">Langkah 1</span>
                        <h4 style="color: #2D2424; font-weight: 700; font-size: 1.15rem; margin-bottom: 18px;">
                            Informasi Dasar Kopi & Asal Kebun
                        </h4>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 6px; display: block;">
                                    Nama Kopi <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model.defer="name" 
                                       placeholder="Contoh: Arabika Kintamani Natural"
                                       style="width: 100%; border: 1px solid #E0D8D0; border-radius: 12px; padding: 10px 14px; font-size: 0.9rem; background: #FAF8F5;">
                                @error('name') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 6px; display: block;">
                                    Jenis Kopi <span class="text-danger">*</span>
                                </label>
                                <select wire:model.defer="jenis_kopi" 
                                        style="width: 100%; border: 1px solid #E0D8D0; border-radius: 12px; padding: 10px 14px; font-size: 0.9rem; background: #FAF8F5;">
                                    <option value="">--- Pilih Jenis Kopi ---</option>
                                    @foreach ($listJenisKopi as $jk)
                                        <option value="{{ $jk->id_jenis_kopi }}">{{ $jk->jenis_kopi }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_kopi') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 6px; display: block;">
                                    Nama Petani / Roastery / Pemilik <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model.defer="nama_supplier" 
                                       style="width: 100%; border: 1px solid #E0D8D0; border-radius: 12px; padding: 10px 14px; font-size: 0.9rem; background: #FAF8F5;">
                                @error('nama_supplier') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 6px; display: block;">
                                    Asal Daerah / Lokasi Kebun <span class="text-danger">*</span>
                                </label>
                                <input type="text" wire:model.defer="alamat" 
                                       placeholder="Contoh: Kintamani, Bali"
                                       style="width: 100%; border: 1px solid #E0D8D0; border-radius: 12px; padding: 10px 14px; font-size: 0.9rem; background: #FAF8F5;">
                                @error('alamat') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 6px; display: block;">
                                    Foto Kemasan / Biji Kopi (Opsional)
                                </label>
                                <input type="file" wire:model="image" 
                                       style="border: 1px solid #E0D8D0; border-radius: 12px; padding: 8px; font-size: 0.85rem; width: 100%; background: #FAF8F5;">
                                @if ($image)
                                    <div class="mt-2">
                                        <img src="{{ $image->temporaryUrl() }}" style="height: 90px; border-radius: 10px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 2. Skor Cupping Karakteristik Rasa -->
                    <div class="mb-4 pb-4" style="border-bottom: 1px solid #F0ECE7;">
                        <span style="font-size: 0.75rem; letter-spacing: 1px; text-transform: uppercase; color: #C67C4E; font-weight: 700; display: block; margin-bottom: 4px;">Langkah 2</span>
                        <h4 style="color: #2D2424; font-weight: 700; font-size: 1.15rem; margin-bottom: 18px;">
                            Penilaian Karakteristik Rasa (Cupping Score)
                        </h4>

                        <div class="row g-4">
                            @foreach ($parameters->where('is_multiple', false) as $parameter)
                                <div class="col-md-6">
                                    <div style="background: #FDFBF9; border: 1px solid #F0ECE7; border-radius: 14px; padding: 16px 20px;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span style="font-size: 0.9rem; font-weight: 700; color: #2D2424;">
                                                {{ $parameter->name }}
                                            </span>
                                            <span style="font-size: 0.75rem; font-weight: 700; background: #EFE9E3; color: #69584D; padding: 2px 10px; border-radius: 12px;">
                                                Skor: {{ $inputs[$parameter->kode] ?? ($parameter->min ?? 1) }}
                                            </span>
                                        </div>

                                        <input type="range" class="form-range"
                                               min="{{ $parameter->min ?? 1 }}"
                                               max="{{ $parameter->max ?? 15 }}"
                                               step="1"
                                               wire:model="inputs.{{ $parameter->kode }}"
                                               style="accent-color: #C67C4E; cursor: pointer;">

                                        <div class="d-flex justify-content-between" style="font-size: 0.72rem; color: #9E948C;">
                                            <span>Rendah ({{ $parameter->min ?? 1 }})</span>
                                            <span>Sedang</span>
                                            <span>Tinggi ({{ $parameter->max ?? 15 }})</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Profil Nuansa / Sub-Parameter (Chips) -->
                    @foreach ($parameters->where('is_multiple', true) as $parameter)
                        <div class="mb-4">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 10px; display: block;">
                                Pilih Profil {{ $parameter->name }} yang Dimiliki Kopi Ini
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($parameter->subParameters as $sub)
                                    @php
                                        $isSelected = isset($inputs[$parameter->kode]) && is_array($inputs[$parameter->kode]) && in_array($sub->subparameter_id, $inputs[$parameter->kode]);
                                    @endphp
                                    <button type="button"
                                            wire:click="toggleSubParam('{{ $parameter->kode }}', {{ $sub->subparameter_id }})"
                                            class="btn btn-sm"
                                            style="border-radius: 20px; padding: 6px 16px; font-size: 0.8rem; font-weight: 600; border: 1px solid {{ $isSelected ? '#C67C4E' : '#E8E2DC' }}; background: {{ $isSelected ? '#C67C4E' : '#FFFFFF' }}; color: {{ $isSelected ? '#FFFFFF' : '#69584D' }}; transition: all 0.15s ease;">
                                        {{ $isSelected ? '✓ ' : '+ ' }}{{ $sub->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Tombol Simpan -->
                    <div class="text-center mt-5">
                        <button type="submit" 
                                style="background: #2D2424; color: #FFFFFF; font-weight: 700; font-size: 0.95rem; border-radius: 35px; padding: 14px 40px; box-shadow: 0 4px 15px rgba(45,36,36,0.15); border: none; cursor: pointer;">
                            <span wire:loading.remove wire:target="saveCoffee">
                                💾 Simpan & Terbitkan ke Katalog Rekomendasi
                            </span>
                            <span wire:loading wire:target="saveCoffee">
                                Menyimpan Kopi...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        @endif

    </div>
</div>