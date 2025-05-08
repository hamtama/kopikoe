<x-filament::page>
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Data Parameters</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-600">Kode</th>
                            <th class="px-4 py-2 text-left text-gray-600">Nama</th>
                            <th class="px-4 py-2 text-left text-gray-600">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parameters as $parameter)
                            <tr class="border-b">
                                <td class="px-4 py-2 text-gray-700">{{ $parameter->kode }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $parameter->name }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ ucfirst($parameter->type) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-bold text-gray-800">Form Input Data Baru</h2>

            <form wire:submit.prevent="normalize" class="space-y-4">
                {{ $this->form }}
                <button type="submit"
                    class="px-4 py-2 font-semibold text-white bg-primary-600 rounded-lg shadow hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-700 focus:ring-opacity-50">
                    Normalisasi
                </button>
            </form>



        </div>

        @if ($normalizedData)
            <div>
                <h2 class="text-xl font-bold text-gray-800">Data Normalisasi</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">Parameter</th>
                                <th class="px-4 py-2 text-left text-gray-600">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($normalizedData as $key => $value)
                                <tr class="border-b">
                                    <td class="px-4 py-2 text-gray-700">{{ $key }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button wire:click="calculateKNN"
                    class="mt-4 px-4 py-2 font-semibold text-white bg-primary-600 rounded-lg shadow hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-700 focus:ring-opacity-50">
                    Hitung KNN
                </button>
            </div>
        @endif

        @if ($recommendations)
            <div>
                <h2 class="text-xl font-bold text-gray-800">Rekomendasi</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">ID</th>
                                <th class="px-4 py-2 text-left text-gray-600">Nama Kopi</th>
                                <th class="px-4 py-2 text-left text-gray-600">Parameter</th>
                                <th class="px-4 py-2 text-left text-gray-600">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recommendations as $recommendation)
                                <tr class="border-b">
                                    <td class="px-4 py-2 text-gray-700">{{ $recommendation['variable_id'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $recommendation['name'] }}</td>
                                    <td class="px-4 py-2 text-gray-700">
                                        {{ json_encode($recommendation['parameters']) }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $recommendation['distance'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>
