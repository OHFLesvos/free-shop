<div class="medium-container">
    <div class="input-group mb-3" style="max-width: 40em">
        <span class="input-group-text">Date range</span>
        <input
            type="date"
            wire:model.lazy="date_start"
            class="form-control w-auto"
            @if($all_time) disabled @endif
            max="{{ $date_end }}"/>
        <input
            type="date"
            wire:model.lazy="date_end"
            class="form-control w-auto"
            @if($all_time) disabled @endif
            min="{{ $date_start }}"
            max="{{ now()->toDateString() }}"/>
        <div class="input-group-text">
            <input
                class="form-check-input mt-0"
                wire:model="all_time"
                type="checkbox"
                value="1"
                id="allTimeInput">
            <label class="form-check-label ms-2" for="allTimeInput">All time</label>
        </div>
    </div>

    @unless($all_time)
        <h2 class="display-6">Between {{ $this->startDateFormatted }} and {{ $this->endDateFormatted }}</h2>
    @else
        <h2 class="display-6">All time</h2>
    @endunless
    <p>
        {{ $customersRegistered }} customers registered.<br>
        {{ $ordersCompleted }} orders completed from {{ $customersWithCompletedOrders }} customers.<br>
        {{ $productsHandedOut }} products handed out.
    </p>
</div>
