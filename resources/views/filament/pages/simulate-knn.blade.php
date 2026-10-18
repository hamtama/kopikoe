<x-filament::page>
    <div class="space-y-6">

        <!-- KARTU PENJELASAN TAHAPAN ALGORITMA KNN -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-primary-50 dark:bg-primary-900/40 text-primary-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Alur Perhitungan Algoritma K-Nearest Neighbors (KNN)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Simulasi ini menghitung tingkat kemiripan data kopi secara matematis melalui 3 tahap utama:</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider block mb-1">Tahap 1: Normalisasi</span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-0">Transformasi skala nilai kriteria ke rentang 0.0 - 1.0 menggunakan rumus Min-Max.</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider block mb-1">Tahap 2: Jarak Euclidean</span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-0">Menghitung jarak perbedaan antara data uji dengan dataset sampel: <code>d = √(Σ(xᵢ - yᵢ)²)</code>.</p>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider block mb-1">Tahap 3: Similarity & Ranking</span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-0">Mengonversi jarak menjadi persentase kecocokan <code>1/(1+d) × 100%</code> dan mengurutkan ranking terdekat.</p>
                </div>
            </div>
        </div>

        <!-- FORMULIR PENGUJIAN DATA BARU -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <form wire:submit.prevent="runFullSimulation" class="space-y-6">
                {{ $this->form }}

                <!-- Tombol Aksi Simulasi -->
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit"
                        class="px-6 py-2.5 font-bold text-white bg-primary-600 hover:bg-primary-500 rounded-lg shadow-sm focus:outline-none transition inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="runFullSimulation">
                            🚀 Jalankan Simulasi KNN Lengkap
                        </span>
                        <span wire:loading wire:target="runFullSimulation" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menghitung Simulasi...
                        </span>
                    </button>

                    <button type="button" wire:click="normalize"
                        class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition">
                        📊 Hanya Normalisasi
                    </button>
                </div>
            </form>
        </div>

        <!-- SECTION 1: TABEL HASIL NORMALISASI DATA UJI -->
        @if ($normalizedData)
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hasil Normalisasi Vektor Data Uji (X')</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai input telah diskalakan ke rentang standar [0.0000 - 1.0000] agar seimbang saat dihitung jaraknya.</p>
                    </div>
                    @if(empty($recommendations))
                        <button type="button" wire:click="calculateKNN"
                            class="px-4 py-2 font-bold text-white bg-success-600 hover:bg-success-500 rounded-lg transition text-sm">
                            Hitung Jarak KNN →
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($normalizedData as $key => $value)
                        @if($key !== 'jenis_kopi')
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-100 dark:border-gray-600 text-center">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase block mb-1">{{ $key }}</span>
                                <span class="text-base font-mono font-bold text-primary-600 dark:text-primary-400">
                                    {{ is_numeric($value) ? number_format((float)$value, 4) : $value }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- SECTION 2: HASIL REKOMENDASI RANKING KNN -->
        @if ($recommendations)
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-6">
                <div class="pb-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-bold text-success-600 uppercase tracking-wider block mb-1">Hasil Perhitungan Selesai</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Rekomendasi Terdekat (K-Nearest Neighbors)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sampel kopi diurutkan berdasarkan jarak Euclidean terendah (tingkat kemiripan tertinggi).</p>
                </div>

                <!-- Showcase Kartu 3 Teratas (Top 3 Podium) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach (array_slice($recommendations, 0, 3) as $i => $top)
                        <div class="p-4 rounded-xl border relative {{ $i === 0 ? 'border-amber-400 bg-amber-50/20 dark:bg-amber-950/20 shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30' }}">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $i === 0 ? 'bg-amber-400 text-amber-950' : 'bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200' }}">
                                    {{ $i === 0 ? '🥇 Ranking 1 (Terdekat)' : ($i === 1 ? '🥈 Ranking 2' : '🥉 Ranking 3') }}
                                </span>
                                <div class="text-right">
                                    <span class="text-xl font-black text-primary-600 dark:text-primary-400 leading-none block">
                                        {{ $top['similarity'] }}%
                                    </span>
                                    <span class="text-xs text-gray-400">Kemiripan</span>
                                </div>
                            </div>

                            <h4 class="font-bold text-base text-gray-900 dark:text-white mb-1">{{ $top['name'] }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ $top['jenis_kopi'] }} • {{ $top['nama_supplier'] }}</p>

                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between text-xs text-gray-500">
                                <span>Jarak Euclidean (d):</span>
                                <code class="font-bold text-gray-800 dark:text-gray-200">{{ $top['distance'] }}</code>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tabel Lengkap Komparasi -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300">Peringkat</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300">Nama Kopi</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300">Jenis & Asal</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300">Jarak Euclidean (d)</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-600 dark:text-gray-300">Kemiripan (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach ($recommendations as $idx => $rec)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 font-bold text-gray-700 dark:text-gray-300">
                                        #{{ $idx + 1 }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $rec['name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $rec['nama_supplier'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded bg-gray-100 dark:bg-gray-700 font-semibold mr-1">
                                            {{ $rec['jenis_kopi'] }}
                                        </span>
                                        {{ $rec['alamat'] }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">
                                        {{ $rec['distance'] }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-primary-600 dark:text-primary-400">
                                        {{ $rec['similarity'] }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Box Kesimpulan Otomatis -->
                @if(isset($recommendations[0]))
                    <div class="p-4 bg-primary-50 dark:bg-primary-950/30 border border-primary-200 dark:border-primary-800 rounded-lg text-sm text-primary-900 dark:text-primary-200">
                        <span class="font-bold block mb-1">💡 Kesimpulan Pengujian:</span>
                        Berdasarkan parameter yang diinput, sampel kopi yang memiliki karakteristik paling mirip dengan skenario uji adalah 
                        <b>{{ $recommendations[0]['name'] }}</b> ({{ $recommendations[0]['jenis_kopi'] }}) dari <b>{{ $recommendations[0]['nama_supplier'] }}</b> 
                        dengan tingkat kemiripan <b>{{ $recommendations[0]['similarity'] }}%</b> (jarak Euclidean terdekat: <code>{{ $recommendations[0]['distance'] }}</code>).
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-filament::page>
