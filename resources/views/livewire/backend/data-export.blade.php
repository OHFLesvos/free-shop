<div>
    <h1>Data Export</h1>
    <form wire:submit.prevent="submit">
        <div class="form-group">
            <label for="format" class="d-block">Please choose a format:</label>
            <select
                id="format"
                wire:model.defer="format"
                class="custom-select"
                style="max-width: 20em;">
                @foreach($formats as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <p class="d-flex justify-content-between align-items-center">
            <button
                type="submit"
                class="btn btn-primary"
                wire:loading.attr="disabled"
                wire:target="submit">
                <x-spinner wire:loading wire:target="submit"/>
                Download
            </button>
        </p>
    </form>
</div>
