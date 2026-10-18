<div class="space-y-6 p-2 text-slate-800">
    <!-- Header / Info Skenario -->
    <div class="rounded-xl bg-slate-50 p-4 border border-slate-200 flex flex-wrap justify-between items-center gap-3">
        <div>
            <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Nama Skenario / Penguji</div>
            <div class="text-lg font-bold text-slate-900">{{ $input['name'] ?? ('Simulasi #' . $record->id) }}</div>
        </div>
        <div>
            <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Target Jenis Kopi</div>
            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                @php
                    $jkId = $input['jenis_kopi'] ?? null;
                    $jk = $jkId ? \App\Models\JenisKopi::find($jkId) : null;
                @endphp
                {{ $jk ? $jk->jenis_kopi : ($jkId ? 'ID: ' . $jkId : 'Semua') }}
            </div>
        </div>
        <div>
            <div class="text-xs uppercase font-semibold text-slate-500 tracking-wider">Waktu Eksekusi</div>
            <div class="text-sm text-slate-600 font-medium">{{ $record->created_at->format('d M Y, H:i') }} WIB</div>
        </div>
    </div>

    <!-- Hasil Rekomendasi Top Match -->
    <div>
        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Hasil Rekomendasi K-Nearest Neighbors (Top {{ count($recs) }})
        </h4>

        @if(!empty($recs) && is_array($recs))
            <div class="space-y-3">
                @foreach($recs as $idx => $rec)
                    @php
                        $dist = $rec['distance'] ?? 0;
                        $similarity = max(0, min(100, round((1 / (1 + (float)$dist)) * 100, 1)));
                        $rankColors = [
                            0 => ['bg' => 'bg-amber-50', 'border' => 'border-amber-300', 'badge' => 'bg-amber-500 text-white', 'title' => 'Peringkat 1 (Best Match)'],
                            1 => ['bg' => 'bg-slate-50', 'border' => 'border-slate-300', 'badge' => 'bg-slate-500 text-white', 'title' => 'Peringkat 2'],
                            2 => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'badge' => 'bg-orange-600 text-white', 'title' => 'Peringkat 3'],
                        ];
                        $style = $rankColors[$idx] ?? ['bg' => 'bg-white', 'border' => 'border-slate-200', 'badge' => 'bg-slate-400 text-white', 'title' => 'Peringkat ' . ($idx + 1)];
                    @endphp
                    <div class="rounded-xl p-4 border {{ $style['border'] }} {{ $style['bg'] }} transition hover:shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $style['badge'] }}">
                                    #{{ $idx + 1 }}
                                </span>
                                <div>
                                    <div class="font-bold text-slate-900 text-base">{{ $rec['name'] ?? 'Kopi Tanpa Nama' }}</div>
                                    <div class="text-xs text-slate-500 flex items-center gap-2">
                                        <span>Supplier: <strong class="text-slate-700">{{ $rec['nama_supplier'] ?? '-' }}</strong></span>
                                        @if(!empty($rec['alamat']))
                                            <span>•</span>
                                            <span>Lokasi: {{ $rec['alamat'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-black text-emerald-600">{{ $similarity }}%</div>
                                <div class="text-xs text-slate-500 font-mono">Euclidean d: {{ round((float)$dist, 4) }}</div>
                            </div>
                        </div>

                        <!-- Progress Bar Kemiripan -->
                        <div class="w-full bg-slate-200 rounded-full h-2 mt-3 overflow-hidden">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $similarity }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center text-slate-400 rounded-xl bg-slate-50 border border-slate-200">
                Tidak ada data rekomendasi tersimpan untuk simulasi ini.
            </div>
        @endif
    </div>
</div>
