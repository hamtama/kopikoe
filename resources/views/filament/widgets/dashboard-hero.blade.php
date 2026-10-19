<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-stone-900 via-neutral-900 to-amber-950 text-white shadow-xl border border-stone-800 p-6 md:p-8">
    <!-- Background Coffee Ambient Circles -->
    <div class="absolute -right-12 -top-12 w-64 h-64 bg-amber-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-1/3 -bottom-12 w-48 h-48 bg-orange-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <!-- Left: Welcome & System Status -->
        <div class="space-y-3 max-w-2xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    SPK Pemilihan Kopi • KNN Engine Aktif
                </span>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-stone-800 text-stone-300">
                    {{ $userRole }}
                </span>
            </div>

            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white flex items-center gap-2">
                <span>{{ $greetingIcon }}</span>
                <span>{{ $greeting }}, <span class="text-amber-400">{{ $userName }}</span></span>
            </h1>

            <p class="text-stone-300 text-sm md:text-base leading-relaxed">
                Selamat datang di panel kendali cerdas <strong class="text-white">Kopikoe</strong>. Pantau dataset sampel kopi, kelola kriteria sensori cupping, dan lakukan pengujian rekomendasi kopi berbasis algoritma K-Nearest Neighbors secara real-time.
            </p>

            <!-- Kesiapan Normalisasi Dataset Bar -->
            <div class="pt-2">
                <div class="flex justify-between items-center text-xs mb-1.5">
                    <span class="text-stone-300 font-medium">Kesiapan Dataset Rekomendasi:</span>
                    <span class="font-bold text-amber-300">{{ $readyKopi }} dari {{ $totalKopi }} Kopi Siap ({{ $percentReady }}%)</span>
                </div>
                <div class="w-full bg-stone-800/80 rounded-full h-2.5 overflow-hidden border border-stone-700/60">
                    <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentReady }}%"></div>
                </div>
            </div>
        </div>

        <!-- Right: Interactive Quick Actions -->
        <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 shrink-0">
            <a href="{{ route('filament.resources.variables.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-amber-500 text-stone-950 hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                + Tambah Sampel Kopi
            </a>

            <a href="{{ route('filament.pages.simulation-knn') }}" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-stone-800/90 text-white hover:bg-stone-700 border border-stone-700 transition">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Jalankan Simulasi KNN
            </a>

            <a href="{{ route('filament.resources.normalizations.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-stone-800/90 text-white hover:bg-stone-700 border border-stone-700 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Matriks Normalisasi
            </a>
        </div>
    </div>
</div>
