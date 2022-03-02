<div>

    @include('livewire.backend.stock-nav')

    <div class="mb-2 d-flex justify-content-end">
        <div class="form-check form-switch">
            <input
                type="checkbox"
                class="form-check-input"
                id="includeOrderChangesInput"
                value="1"
                wire:model="includeOrderChanges">
            <label class="form-check-label" for="includeOrderChangesInput">Include changes from orders</label>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm">
            <thead>
                <th class="fit">Date</th>
                <th>Product</th>
                <th class="fit text-end">Change</th>
                <th class="fit text-end" title="After change">Total</th>
                <th>Description</th>
                <th class="fit">User</th>
                <th class="fit">Order</th>
            </thead>
            <tbody>
                @forelse($changes as $change)
                    <tr>
                        <td class="fit" title="{{ $change->created_at->toUserTimezone()->isoFormat('LLLL') }}">{{ $change->created_at->diffForHumans() }}</td>
                        <td>{{ $change->product->name }}</td>
                        <td class="fit text-end">
                            @if ($change->quantity > 0)
                                <span class="text-success">+{{ $change->quantity }}</span>
                            @else
                                <span class="text-danger">{{ $change->quantity }}</span>
                            @endif
                        </td>
                        <td class="fit text-end">{{ $change->total }}</td>
                        <td>{{ $change->description }}</td>
                        <td class="fit">{{ optional($change->user)->name }}</td>
                        <td class="fit">
                            @isset($change->order)
                                <a href="{{ route('backend.orders.show', $change->order) }}">#{{ optional($change->order)->id }}</a>
                            @endisset
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <em>No changes registered.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($changes->hasPages())
        <div class="row">
            <div class="col overflow-auto">
                {{ $changes->onEachSide(2)->links() }}
            </div>
            <div class="col-sm-auto">
                <small>Showing {{ $changes->firstItem() }} to {{ $changes->lastItem() }} of
                    {{ $changes->total() }} records</small>
            </div>
        </div>
    @elseif($changes->total() > 0)
        <div class="d-flex justify-content-end">
            <small>Showing {{ $changes->total() }} records</small>
        </div>
    @endif
</div>
