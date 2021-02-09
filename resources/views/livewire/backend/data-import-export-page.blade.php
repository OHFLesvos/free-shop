<div class="medium-container">
    <div class="row">
        <div class="col-md">
            <form wire:submit.prevent="import">
                <x-card title="Import">
                    <div class="mb-3">
                        <label for="uploadInput" class="form-label">Please choose a file for import:</label>
                        <input
                            type="file"
                            class="form-control @error('upload') is-invalid @enderror"
                            wire:model="upload"
                            id="uploadInput"
                            accept=".xlsx,.xls,.ods,.csv">
                        @error('upload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-check form-switch">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="deleteExistingDataInput"
                            value="1"
                            wire:model.defer="delete_existing_data">
                        <label class="form-check-label" for="deleteExistingDataInput">Delete existing data</label>
                    </div>
                    <x-slot name="footer">
                        <div class="text-end">
                            <span wire:loading wire:target="upload" class="me-2"><x-spinner/></span>
                            <button
                                type="submit"
                                class="btn btn-primary"
                                wire:loading.attr="disabled"
                                @unless(isset($upload)) disabled @endunless
                                wire:target="import">
                                <x-spinner wire:loading wire:target="import"/>
                                Import
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </form>
            @if (count($errors) > 0)
                <x-alert type="warning" dismissible>
                    Validation failed
                    <ul class="mb-0 pb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif
            @if(session()->has('message'))
                <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
            @endif
        </div>

        <div class="col-md">
            <form wire:submit.prevent="export">
                <x-card title="Export">
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
                        <div class="text-end">
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
</div>
