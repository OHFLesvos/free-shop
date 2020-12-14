<div>
    <h1>Data Import & Export</h1>
    <div class="row">
        <div class="col-md">
            <form wire:submit.prevent="import">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">Import</div>
                    <div class="card-body pb-2">
                        <div class="custom-file mb-3">
                            <input
                                type="file"
                                class="custom-file-input @error('upload') is-invalid @enderror"
                                wire:model="upload"
                                id="uploadInput"
                                accept=".xlsx,.xls,.ods,.csv">
                            <label class="custom-file-label" for="uploadInput">Choose file</label>
                        </div>
                        @error('upload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="custom-control custom-checkbox mb-3">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="deleteExistingDataInput"
                                value="1"
                                wire:model.defer="delete_existing_data">
                            <label class="custom-control-label" for="deleteExistingDataInput">Delete existing data</label>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <span wire:loading wire:target="upload" class="mr-2"><x-spinner/></span>
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
                </div>
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
                <div class="card shadow-sm mb-4">
                    <div class="card-header">Export</div>
                    <div class="card-body pb-2">
                        <div class="form-group">
                            <label for="format" class="d-block">Please choose a format:</label>
                            <select
                                id="format"
                                wire:model.defer="format"
                                class="custom-select @error('format') is-invalid @enderror"
                                style="max-width: 20em;">
                                @foreach($formats as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="export">
                            <x-spinner wire:loading wire:target="export"/>
                            Download
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
