<span class="d-flex align-items-center">
    <button
        type="submit"
        class="btn btn-primary">
        <x-spinner wire:loading wire:target="submit"/>
        {{ $slot }}
    </button>
    @if(session()->has('submitMessage'))
        <small class="text-success ms-3" wire:loading.remove wire:target="submit">
            <x-icon icon="check"/>
            {{ session()->get('submitMessage') }}
        </small>
    @endif
</span>
