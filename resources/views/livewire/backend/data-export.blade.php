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
            >
                <span wire:loading wire:target="submit">Processing...</span>
                <span wire:loading.remove wire:target="submit">Download</span>
            </button>
        </p>
    </form>
</div>
