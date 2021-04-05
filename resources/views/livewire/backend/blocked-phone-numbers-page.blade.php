<div>
    @include('livewire.backend.configuration-nav')
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm">
            <caption>{{ $entries->total() }} blocked phone numbers found</caption>
            <thead>
                <th>Number</th>
                <th>Reason</th>
                <th>Added</th>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td class="fit">
                            {{ $entry->phone }}
                        </td>
                        <td>
                            {{ $entry->reason }}
                        </td>
                        <td class="fit">
                            <x-date-time-info :value="$entry->created_at" line-break />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>No blocked phone numbers registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="overflow-auto">{{ $entries->links() }}</div>
</div>
