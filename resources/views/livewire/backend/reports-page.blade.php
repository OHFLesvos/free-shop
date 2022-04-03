<div class="medium-container">
    <div class="row">
        <div class="col">
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
                    wire:model.lazy="dateStart"
                    class="form-control w-auto"
                    @isset($dateEnd) max="{{ $dateEnd }}" @endisset
                />
                <input
                    type="date"
                    wire:model.lazy="dateEnd"
                    class="form-control w-auto"
                    @isset($dateStart) min="{{ $dateStart }}" @endisset
                    max="{{ now()->toDateString() }}"
                />
            </div>
        </div>
        <div class="col-md-auto mb-3">
            <button type="button"
                class="btn btn-primary"
                wire:click="generatePdf()"
                wire:loading.attr="disabled"
                wire:target="generatePdf">
                <x-spinner wire:loading wire:target="generatePdf"/>
                Download PDF
            </button>
        </div>
    </div>
    @include('backend.include.report', ['dateRangeTitle' => $this->dateRangeTitle ])

    <h2 class="display-6">
        General Statistics
    </h2>

    @if($userAgents['browser']->isNotEmpty())
        <table class="table table-bordered bg-white shadow-sm">
            <thead>
                <th>Browser</th>
                <th class="fit text-end">Quantity</th>
                <th class="fit text-end">Percent</th>
            </thead>
            <tbody>
                @foreach($userAgents['browser'] as $browser => $quantity)
                    <tr>
                        <td>{{ $browser }}</td>
                        <td class="fit text-end">
                            {{ number_format($quantity) }}
                        </td>
                        <td class="fit text-end">
                            {{ round($quantity / $userAgents['browser']->sum() * 100, 1) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($userAgents['os']->isNotEmpty())
        <table class="table table-bordered bg-white shadow-sm">
            <thead>
                <th>Operating System</th>
                <th class="fit text-end">Quantity</th>
                <th class="fit text-end">Percent</th>
            </thead>
            <tbody>
                @foreach($userAgents['os'] as $os => $quantity)
                    <tr>
                        <td>{{ $os }}</td>
                        <td class="fit text-end">
                            {{ number_format($quantity) }}
                        </td>
                        <td class="fit text-end">
                            {{ round($quantity / $userAgents['os']->sum() * 100, 1) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($customerLocales->isNotEmpty())
        @inject('localization', 'App\Services\LocalizationService')
        <table class="table table-bordered bg-white shadow-sm">
            <thead>
                <th>Language</th>
                <th class="fit text-end">Quantity</th>
                <th class="fit text-end">Percent</th>
            </thead>
            <tbody>
                @foreach($customerLocales as $locale => $quantity)
                    <tr>
                        <td>
                            {{ $localization->getLanguageName($locale) }}
                        </td>
                        <td class="fit text-end">
                            {{ number_format($quantity) }}
                        </td>
                        <td class="fit text-end">
                            {{ round($quantity / $customerLocales->sum() * 100, 1) }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($communicationChannels->isNotEmpty())
    <table class="table table-bordered bg-white shadow-sm">
        <thead>
            <th>Communication channels</th>
            <th class="fit text-end">Quantity</th>
            <th class="fit text-end">Percent</th>
        </thead>
        <tbody>
            @foreach($communicationChannels as $channel => $quantity)
                <tr>
                    <td>
                        {{ $channel }}
                    </td>
                    <td class="fit text-end">
                        {{ number_format($quantity) }}
                    </td>
                    <td class="fit text-end">
                        {{ round($quantity / $communicationChannels->sum() * 100, 1) }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

</div>
