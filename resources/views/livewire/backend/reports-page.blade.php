<div class="medium-container">
    <div class="input-group mb-3" style="max-width: 30em">
        <span class="input-group-text">Date range</span>
        <input
            type="date"
            wire:model.lazy="date_start"
            class="form-control w-auto"
            max="{{ $date_end }}"/>
        <input
            type="date"
            wire:model.lazy="date_end"
            class="form-control w-auto"
            min="{{ $date_start }}"
            max="{{ now()->toDateString() }}"/>
    </div>

    <h2 class="display-6">Between {{ $this->startDateFormatted }} and {{ $this->endDateFormatted }}</h2>
    <p>
        {{ $ordersCompletedInDateRange }} orders completed.<br>
        {{ $customersRegisteredInDateRange }} new customers registered.<br>
        {{ $productsHandedOutInDateRange }} products handed out.
    </p>

    <h2 class="display-6">Currently</h2>
    <p>
        {{ $productsAvailableCurrently }} different products available.<br>
        {{ $ordersInProgress }} orders in progress.
    </p>

    <h2 class="display-6">All time</h2>
    <p>
        {{ $customersRegistered }} customers registered.<br>
        {{ $ordersCompletedInTotal }} orders completed
        from {{ $customersWithCompletedOrdersInTotal }} customers.<br>
        {{ $productsHandedOut }} products handed out.
    </p>
</div>
