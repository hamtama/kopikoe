<div style="grid-column: 1 / -1; width: 100%; background: linear-gradient(135deg, #1C1917 0%, #29231E 50%, #3D2619 100%); border: 1px solid #4A3B32; border-radius: 16px; padding: 22px 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.12); box-sizing: border-box;">
    
    <!-- Row 1: Badges & Status -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
            <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(217, 119, 6, 0.25); color: #FDE68A; border: 1px solid rgba(217, 119, 6, 0.45); letter-spacing: 0.5px;">
                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #F59E0B;"></span>
                SPK Pemilihan Kopi • KNN Engine Aktif
            </span>
            <span style="display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.72rem; font-weight: 600; background: rgba(255, 255, 255, 0.12); color: #E5E7EB;">
                {{ $userRole }}
            </span>
        </div>

        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.35);">
            <span>✓ {{ $readyKopi }} dari {{ $totalKopi }} Kopi Siap ({{ $percentReady }}% Normalisasi)</span>
        </div>
    </div>

    <!-- Row 2: Greeting & Quick Action Buttons -->
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
        <!-- Left: Text Greeting -->
        <div style="max-width: 600px;">
            <h2 style="font-size: 1.45rem; font-weight: 800; color: #FFFFFF; margin: 0 0 6px 0; display: flex; align-items: center; gap: 8px; line-height: 1.2;">
                <span>{{ $greetingIcon }}</span>
                <span>{{ $greeting }}, <span style="color: #F59E0B;">{{ $userName }}</span></span>
            </h2>
            <p style="font-size: 0.86rem; color: #D6CCC2; margin: 0; line-height: 1.4;">
                Kelola dataset sampel kopi, pantau kriteria sensori cupping, dan jalankan simulasi rekomendasi K-Nearest Neighbors secara terpadu.
            </p>
        </div>

        <!-- Right: Action Buttons -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
            <a href="{{ route('filament.resources.variables.create') }}" 
               style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 10px; font-size: 0.82rem; font-weight: 700; background: #C67C4E; color: #FFFFFF; text-decoration: none; box-shadow: 0 2px 10px rgba(198, 124, 78, 0.35); transition: all 0.2s;">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                + Tambah Kopi
            </a>

            <a href="{{ route('filament.pages.simulation-knn') }}" 
               style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 600; background: rgba(255, 255, 255, 0.1); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.2); text-decoration: none; transition: all 0.2s;">
                <svg style="width: 15px; height: 15px; color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Simulasi KNN
            </a>

            <a href="{{ route('filament.resources.normalizations.index') }}" 
               style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 10px; font-size: 0.82rem; font-weight: 600; background: rgba(255, 255, 255, 0.1); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.2); text-decoration: none; transition: all 0.2s;">
                <svg style="width: 15px; height: 15px; color: #34D399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Matriks
            </a>
        </div>
    </div>

    <!-- Row 3: Slim Progress Bar -->
    <div style="margin-top: 16px; width: 100%; background: rgba(255, 255, 255, 0.1); border-radius: 9999px; height: 5px; overflow: hidden;">
        <div style="height: 5px; border-radius: 9999px; background: linear-gradient(90deg, #F59E0B, #10B981); width: {{ $percentReady }}%;"></div>
    </div>

</div>
