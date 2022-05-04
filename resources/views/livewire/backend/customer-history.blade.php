<div>
    @if ($audits->isNotEmpty())
        <ul class="list-group shadow-sm mb-4">
            @foreach ($audits as $audit)
                <li class="list-group-item">
                    On <strong>
                        <x-date-time-info :value="$audit->created_at" />
                    </strong>
                    <strong>{{ optional($audit->user)->name ?? 'Unknown' }}</strong>
                    @if ($audit->event == 'created')
                        registered the customer.
                    @elseif($audit->event == 'updated')
                        updated the customer and changed
                        @php
                            $modified = $audit->getModified();
                        @endphp
                        @foreach ($modified as $key => $val)
                            <em>{{ $key }}</em>
                            @isset($val['old']) from <code>{{ $val['old'] }}</code> @endisset
                            to <code>{{ $val['new'] }}</code>@if ($loop->last).@else,@endif
                        @endforeach
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="overflow-auto">{{ $audits->onEachSide(2)->links() }}</div>
    @else
        <x-alert type="info">
            No entries registered.
        </x-alert>
    @endif
</div>
