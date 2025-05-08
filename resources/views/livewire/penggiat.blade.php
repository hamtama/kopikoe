<x-app-layout>
    <div class="d-flex justify-content-center">
        <div class="card shadow align-center mt-4 border-secondary w-75">
            <div class="card-header bg-secondary text-white">
                <strong>Formulir Sample Kopi (Penggiat)</strong>
            </div>

            <div class="card-body">
                <form wire:submit.prevent="calculateKNN">
                    <div class="row gy-3"> <!-- Adds vertical spacing between rows -->

                        @foreach ($parameters as $parameter)
                            <!-- Makes the form responsive -->

                            @if ($parameter->is_multiple)
                                <div class="col-12 mb-3">
                                    <div class="row">

                                        <div class="col-md-2" style="width: 20%">
                                            <label class="form-label text-muted">
                                                {{ $parameter->name }}
                                            </label>
                                        </div>
                                        <div class="col-md-8">


                                            <div class="form-check row">
                                                @foreach ($parameter->sub_parameters as $subParam)
                                                    <label class="form-check-label me-3">
                                                        <input type="checkbox" class="form-check-input"
                                                            wire:model.defer="inputs.{{ $parameter->kode }}[]"
                                                            value="{{ $subParam->subparameter_id }}">
                                                        {{ $subParam->name }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row mb-60">
                                    <x-level-input :name="'inputs[' . $parameter->kode . ']'" :id="$parameter->kode" :displayLabel="$parameter->name"
                                        :numberRange="[$parameter->min, $parameter->max]" wire:model.defer="inputs.{{ $parameter->kode }}" />
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-start mt-4">
                        <button type="button" wire:click="normalize" class="btn btn-primary me-2">
                            Normalize
                        </button>
                        <button type="submit" class="btn btn-success">
                            Calculate KNN
                        </button>
                    </div>
                </form>
            </div>

            {{-- Uncomment to display recommendations if available --}}
            {{-- @if (!empty($recommendations))
                <div class="mt-4">
                    <h2 class="text-xl font-semibold">Recommendations</h2>
                    <ul class="list-unstyled">
                        @foreach ($recommendations as $recommendation)
                            <li>{{ $recommendation['name'] }} (Score: {{ $recommendation['score'] }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
        </div>
    </div>
</x-app-layout>
