@props([
    'name',
    'id',
    'disabled' => false,
    'displayLabel' => '',
    'option' => [], // Daftar opsi checkbox dengan format ['value' => 'label']
    'checkedColor' => '#ff845f', // Warna saat checkbox dicentang
])

<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
    <!-- Label di sebelah kiri -->
    <div class="flex flex-col justify-center items-start">
        <x-input-label for="{{ $id }}" :value="$displayLabel" />
    </div>

    <!-- Input di sebelah kanan -->
    <div class="col-span-2 flex flex-col justify-center">
        @foreach ($option as $value => $label)
            <label class="flex items-center space-x-3">
                <input type="checkbox" value="{{ $value }}" {{ $disabled ? 'disabled' : '' }}
                    class="form-checkbox h-5 w-5 text-{{ $checkedColor }} rounded border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:ring-{{ $checkedColor }}"
                    wire:model.defer="{{ $name }}">
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>




{{-- ==================================== --}}
{{-- @props([
    'name',
    'id',
    'disabled' => false,
    'displayLabel' => '',
    'option' => [], // Daftar opsi checkbox dengan format ['value' => 'label']
    'checkedColor' => '#ff845f', // Warna saat checkbox dicentang
])

<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
    <!-- Label di sebelah kiri -->
    <div class="flex flex-col justify-center items-start">
        <x-input-label for="{{ $id }}" :value="$displayLabel" />
    </div>

    <!-- Input di sebelah kanan -->
    <div class="col-span-2 flex flex-col justify-center">
        <div class="checkbox-group">
            @foreach ($option as $value => $label)
                <label class="custom-checkbox-label">
                    <input type="checkbox" name="{{ $name }}[]" id="{{ $id }}-{{ $value }}"
                        value="{{ $value }}" {{ $disabled ? 'disabled' : '' }}
                        class="form-checkbox h-5 w-5 rounded shadow-sm mr-2" wire:model.defer="{{ $name }}">
                    <span class="custom-checkbox-box">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>

<style>
    .checkbox-group {
        display: flex;
        flex-direction: column;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        margin-bottom: 0.1rem;
    }

    .checkbox-box {
        display: flex;
        align-items: center;
        padding: 0.1rem 1rem;
        border-radius: 0.25rem;
        background-color: #f5f5f5;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .checkbox-box:hover {
        background-color: #e5e5e5;
    }

    .checkbox-label input[type="checkbox"]:checked+.checkbox-box {
        background-color: {{ $checkedColor }};
        color: white;
    }

    .checkbox-label input[type="checkbox"]:checked {
        background-color: {{ $checkedColor }};
        border-color: {{ $checkedColor }};
    }
</style> --}}
