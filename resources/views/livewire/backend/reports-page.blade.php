<div class="medium-container">
    <div class="input-group mb-3" style="max-width: 30em">
        <button
            class="btn btn-outline-secondary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">{{ $ranges[$range] ?? 'Date range' }}</button>
        <ul class="dropdown-menu">
            @foreach($ranges as $key => $label)
                <li>
                    <button
                        class="dropdown-item @if($range == $key) active @endif"
                        type="button"
                        wire:click="$set('range', '{{ $key }}')">
                        {{ $label }}
                    </button>
                </li>
            @endforeach
        </ul>
        <input
            type="date"
            wire:model.lazy="date_start"
            class="form-control w-auto"
            @isset($date_end) max="{{ $date_end }}" @endisset
        />
        <input
            type="date"
            wire:model.lazy="date_end"
            class="form-control w-auto"
            @isset($date_start) min="{{ $date_start }}" @endisset
            max="{{ now()->toDateString() }}"
        />
    </div>
    @include('backend.include.report', ['dateRangeTitle' => $this->dateRangeTitle ])
    <p>
        <button type="button"
            class="btn btn-primary"
            wire:click="generatePdf()"
            wire:loading.attr="disabled"
            wire:target="generatePdf">
            <x-spinner wire:loading wire:target="generatePdf"/>
            Download PDF
        </button>
    </p>
</div>
