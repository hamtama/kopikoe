<div style="background-color: #FAF8F5; min-height: 100vh; padding: 40px 0;">
    <div class="container" style="max-width: 1050px;">
        
        <!-- Header Minimalis & Elegan -->
        <div class="text-center mb-5">
            <span style="font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase; color: #8C6D58; font-weight: 700; display: block; margin-bottom: 6px;">
                Sistem Rekomendasi Kopi
            </span>
            <h1 style="color: #2D2424; font-weight: 700; font-size: 2.2rem; margin-bottom: 10px;">
                Temukan Kopi Sesuai Seleramu
            </h1>
            <p style="color: #7A726D; font-size: 0.95rem; max-width: 580px; margin: 0 auto;">
                Sesuaikan preferensi rasa yang Anda sukai di bawah ini. Algoritma kami akan mencocokkan kopi yang paling pas untuk Anda.
            </p>
        </div>

        <!-- HASIL REKOMENDASI (Tampil di bagian atas secara elegan saat dicari) -->
        @if ($hasSearched)
            <div id="hasil-rekomendasi" class="mb-5">
                <div style="background: #ffffff; border-radius: 20px; border: 1px solid #ECE7E1; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3" style="border-bottom: 1px solid #F0ECE7;">
                        <div>
                            <span style="color: #C67C4E; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Hasil Pencocokan</span>
                            <h3 style="color: #2D2424; font-weight: 700; margin: 0; font-size: 1.4rem;">Rekomendasi Terbaik Untuk Anda</h3>
                        </div>
                        <button type="button" wire:click="resetSearch" class="btn btn-sm" 
                                style="border: 1px solid #D6CECE; color: #5C5252; border-radius: 30px; padding: 6px 18px; font-weight: 600;">
                            ✕ Atur Ulang
                        </button>
                    </div>

                    @if (empty($recommendations))
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Tidak ditemukan kopi yang cocok dengan kombinasi filter ini. Coba pilih jenis kopi lainnya.</p>
                        </div>
                    @else
                        <!-- Kartu Rekomendasi Grid 3-4 Kolom -->
                        <div class="row g-4">
                            @foreach ($recommendations as $index => $rec)
                                <div class="col-md-6 col-lg-3">
                                    <div style="background: #FFFFFF; border-radius: 16px; border: 1px solid {{ $index === 0 ? '#C67C4E' : '#ECE7E1' }}; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: all 0.2s ease; box-shadow: {{ $index === 0 ? '0 8px 24px rgba(198,124,78,0.15)' : 'none' }};">
                                        
                                        <!-- Foto Kopi / Badge -->
                                        <div style="height: 140px; background-color: #F4EFEB; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                            @if(!empty($rec['image']))
                                                <img src="{{ asset('storage/' . $rec['image']) }}" alt="{{ $rec['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <i class="fi-rs-coffee" style="font-size: 2.8rem; color: #C4B5A8;"></i>
                                            @endif

                                            <!-- Badge Ranking -->
                                            <span style="position: absolute; top: 10px; left: 10px; background: {{ $index === 0 ? '#2D2424' : '#FFFFFF' }}; color: {{ $index === 0 ? '#FFFFFF' : '#2D2424' }}; font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                                                {{ $index === 0 ? '★ Pilihan #1' : '#' . ($index + 1) }}
                                            </span>

                                            <!-- Badge Kecocokan -->
                                            <span style="position: absolute; bottom: 10px; right: 10px; background: rgba(255,255,255,0.92); color: #C67C4E; font-size: 0.75rem; font-weight: 800; padding: 3px 8px; border-radius: 8px;">
                                                {{ $rec['similarity'] }}% Cocok
                                            </span>
                                        </div>

                                        <!-- Informasi Kopi -->
                                        <div style="padding: 18px; display: flex; flex-direction: column; flex-grow: 1;">
                                            <span style="font-size: 0.7rem; color: #8C6D58; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">
                                                {{ $rec['jenis_kopi'] }}
                                            </span>
                                            <h5 style="color: #2D2424; font-weight: 700; font-size: 1.05rem; margin-bottom: 8px; line-height: 1.3;">
                                                {{ $rec['name'] }}
                                            </h5>
                                            
                                            <div style="font-size: 0.8rem; color: #7A726D; margin-bottom: 14px;">
                                                <div class="text-truncate">📍 {{ $rec['alamat'] }}</div>
                                                <div class="text-truncate">🏷️ {{ $rec['nama_supplier'] }}</div>
                                            </div>

                                            <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #F5F0EB;">
                                                <a href="{{ route('product.details', $rec['id']) }}" class="btn w-100" 
                                                   style="background: #2D2424; color: #FFFFFF; font-size: 0.82rem; font-weight: 600; border-radius: 25px; padding: 7px 0;">
                                                    Lihat Detail
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
        @endif

        <!-- FORMULIR FILTER & PREFERENSI RASA -->
        <div style="background: #ffffff; border-radius: 24px; border: 1px solid #ECE7E1; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
            <form wire:submit.prevent="calculateRecommendation">
                
                <!-- 1. Pilihan Jenis Kopi (Pills Button) -->
                <div class="mb-4 pb-4" style="border-bottom: 1px solid #F0ECE7;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 12px; display: block;">
                        1. Pilih Jenis Kopi yang Diinginkan
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" wire:click="setJenisKopi('')"
                                class="btn btn-sm"
                                style="border-radius: 30px; padding: 8px 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid {{ $jenis_kopi_id === '' ? '#2D2424' : '#E0D8D0' }}; background: {{ $jenis_kopi_id === '' ? '#2D2424' : '#FFFFFF' }}; color: {{ $jenis_kopi_id === '' ? '#FFFFFF' : '#5C5252' }};">
                            Semua Kopi
                        </button>
                        @foreach ($listJenisKopi as $jk)
                            <button type="button" wire:click="setJenisKopi('{{ $jk->id_jenis_kopi }}')"
                                    class="btn btn-sm"
                                    style="border-radius: 30px; padding: 8px 20px; font-weight: 600; font-size: 0.85rem; border: 1px solid {{ $jenis_kopi_id == $jk->id_jenis_kopi ? '#2D2424' : '#E0D8D0' }}; background: {{ $jenis_kopi_id == $jk->id_jenis_kopi ? '#2D2424' : '#FFFFFF' }}; color: {{ $jenis_kopi_id == $jk->id_jenis_kopi ? '#FFFFFF' : '#5C5252' }};">
                                {{ $jk->jenis_kopi }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Parameter Intensitas Rasa (Slider Rapi 2 Kolom) -->
                <div class="mb-4 pb-4" style="border-bottom: 1px solid #F0ECE7;">
                    <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 14px; display: block;">
                        2. Tentukan Tingkat Karakteristik Rasa
                    </label>

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
                                        <span>Halus ({{ $parameter->min ?? 1 }})</span>
                                        <span>Sedang</span>
                                        <span>Pekat ({{ $parameter->max ?? 15 }})</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Catatan Aroma & Profil Spesifik (Chips Sederhana) -->
                @foreach ($parameters->where('is_multiple', true) as $parameter)
                    <div class="mb-4">
                        <label style="font-size: 0.85rem; font-weight: 700; color: #2D2424; margin-bottom: 10px; display: block;">
                            3. Pilih Nuansa {{ $parameter->name }} yang Anda Sukai (Opsional)
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

                <!-- Tombol Submit Elegan -->
                <div class="text-center mt-5">
                    <button type="submit" class="btn"
                            style="background: #2D2424; color: #FFFFFF; font-weight: 700; font-size: 0.95rem; border-radius: 35px; padding: 14px 40px; box-shadow: 0 4px 15px rgba(45,36,36,0.15); border: none;">
                        <span wire:loading.remove wire:target="calculateRecommendation">
                            Temukan Rekomendasi Kopi
                        </span>
                        <span wire:loading wire:target="calculateRecommendation">
                            Mencari Rekomendasi...
                        </span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<!-- Script Auto-Scroll Mulus ke Hasil -->
<script>
    window.addEventListener('scroll-to-results', event => {
        setTimeout(() => {
            const el = document.getElementById('hasil-rekomendasi');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    });
</script>