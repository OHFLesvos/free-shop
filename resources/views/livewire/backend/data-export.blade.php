<div>
    <h1>Data Export</h1>
    <form wire:submit.prevent="submit">
        <div class="form-group">
            <label for="format">Please choose a format:</label>
            <select
                id="format"
                wire:model.defer="format"
                class="custom-select">
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
                wire:target="submit"
            >
                <x-icon-progress wire:loading wire:target="submit"/>
                Download
            </button>
        </p>
    </form>
</div>
