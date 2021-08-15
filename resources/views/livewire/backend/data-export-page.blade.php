<div class="small-container">
    <div class="row">
        @can('export data')
            <div class="col-md">
                <form wire:submit.prevent="export">
                    <x-card title="Export">
                        <p class="form-label">Type:</p>
                        <div class="mb-3">
                            @foreach($this->types as $key => $value)
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        id="typeInput_{{ $key }}"
                                        value="{{ $key }}"
                                        wire:model="type">
                                    <label class="form-check-label" for="typeInput_{{ $key }}">
                                        {{ $value['label'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <label for="format" class="form-label">Please choose an export format:</label>
                            <select
                                id="format"
                                wire:model.defer="format"
                                class="form-select @error('format') is-invalid @enderror"
                                style="max-width: 20em;">
                                @foreach($formats as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <x-slot name="footer">
                            <div class="d-flex justify-content-end">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="export">
                                    <x-spinner wire:loading wire:target="export"/>
                                    Download
                                </button>
                            </div>
                        </x-slot>
                    </x-card>
                </form>
            </div>
        </div>
    @endcan
</div>
