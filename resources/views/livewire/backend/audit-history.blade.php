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
                        registered the {{ $label }}.
                    @elseif($audit->event == 'updated')
                        updated the {{ $label }} and changed
                        @foreach ($audit->getModified() as $key => $val)
                            <em>{{ $key }}</em>
                            @isset($val['old']) from <code>{{ $val['old'] }}</code> @endisset
                            to <code>{{ $val['new'] }}</code>@if ($loop->last).@else,@endif
                        @endforeach
                    @elseif ($audit->event == 'deleted')
                        removed the {{ $label }}.
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
