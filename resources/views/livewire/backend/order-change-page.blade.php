<div class="small-container">
    <x-card title="Change order #{{ $order->id }}">
        <p class="form-label">New status:</p>
        @foreach($statuses as $key)
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="radio"
                    id="newStatusInput_{{ $key }}"
                    @if($key == $order->status) autofocus @endif
                    value="{{ $key }}"
                    wire:model="newStatus">
                <label class="form-check-label" for="newStatusInput_{{ $key }}">
                    <x-order-status-label :value="$key" />
                    @if($key == $order->status)
                        (current)
                    @endif
                </label>
            </div>
        @endforeach
        <x-slot name="footer">
            <div class="d-flex justify-content-end">
                <span>
                    <a
                        href="{{ route('backend.orders.show', $order) }}" c
                        class="btn btn-link">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:target="submit"
                        wire:loading.attr="disabled"
                        wire:click="submit">
                        <x-spinner wire:loading wire:target="submit"/>
                        Apply
                    </button>
                </span>
            </div>
        </x-slot>
    </x-card>
</div>
