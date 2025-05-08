@props([
    'name',
    'id',
    'disabled' => false,
    'displayLabel' => '',
    'labels' => ['Low', 'Medium', 'High'],
    'numberRange' => [1, 15],
    'colors' => ['#ffccbc', '#ffa185', '#ff845f'],
])

<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
    <!-- Label di sebelah kiri -->
    <div class="flex flex-col justify-center items-start">
        <x-input-label for="{{ $id }}" :value="$displayLabel" /> <!-- Gunakan label yang dikirim dari controller -->
        <small class="text-muted">
            <h5>Intensity</h5>
        </small>
    </div>

    <!-- Input di sebelah kanan -->
    <div class="col-span-3 flex flex-col justify-center">
        <div class="level-input">
            <div class="scale">
                <div class="level-labels">
                    @foreach ($labels as $label)
                        <span class="text-gray-700">{{ $label }}</span>
                    @endforeach
                </div>
                <div class="radio-buttons">
                    @for ($i = $numberRange[0]; $i <= $numberRange[1]; $i++)
                        <label class="radio-label">
                            <input type="radio" name="{{ $name }}"
                                id="{{ $id }}-{{ $i }}" value="{{ $i }}"
                                {{ $disabled ? 'disabled' : '' }} class="hidden peer"
                                wire:model.defer="{{ $name }}">
                            <div class="radio-button"
                                style="background-color: {{ $i <= 5 ? $colors[0] : ($i <= 10 ? $colors[1] : $colors[2]) }}">
                                {{ $i }}
                            </div>
                        </label>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .level-input {
        width: 100%;
        text-align: center;
    }

    .level-input .scale {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-top: 8px;
    }

    .level-input .scale .level-labels {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 8px;
    }

    .level-input .scale .radio-buttons {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .level-input .scale .radio-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .level-input .scale input[type="radio"] {
        display: none;
    }

    .level-input .scale .peer {
        display: inline-block;
    }

    .level-input .scale .radio-button {
        width: 3.4em;
        height: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 0%;
        cursor: pointer;
        position: relative;
        transition: background-color 0.3s ease;
    }

    /* Hover effect */
    .level-input .scale .radio-label .radio-button:hover {
        transform: scale(1.1);
    }

    /* Checked state */
    .radio-label input[type="radio"]:checked+.radio-button {
        background-color: #801e00;
        /* Change to your preferred checked color */
        color: white;
        border-color: #fe6a3d;
    }
</style>
