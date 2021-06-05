<div>
    @if (session()->has('error'))
        <x-alert type="danger" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="row g-3 mb-3">
        <div class="col-md">
            <div class="input-group">
                <input
                    type="search"
                    wire:model.debounce.500ms="search"
                    placeholder="Search orders (ID, remarks, customer name/ID number/phone)..."
                    wire:keydown.escape="$set('search', '')" class="form-control" />
                <span class="input-group-text" wire:loading wire:target="search">
                    <x-spinner />
                </span>
                @empty($search)
                    <span class="input-group-text" wire:loading.remove wire:target="search">
                        {{ $orders->total() }} total
                    </span>
                @else
                    <span class="input-group-text @if($orders->isEmpty()) bg-warning @else bg-success text-light @endif" wire:loading.remove wire:target="search">
                        {{ $orders->total() }} results
                    </span>
                @endif
            </div>
        </div>
        <div class="col-auto overflow-auto">
            <div class="btn-group" role="group">
                <button type="button" class="btn @if ($status == '' ) btn-secondary @else btn-outline-secondary @endif"
                    wire:click="$set('status', '')" wire:loading.attr="disabled">Any</button>
                <button type="button" class="btn @if ($status == 'new' ) btn-warning @else btn-outline-warning @endif"
                    wire:click="$set('status', 'new')" wire:loading.attr="disabled">New</button>
                <button type="button" class="btn @if ($status == 'ready' ) btn-info @else btn-outline-info @endif"
                    wire:click="$set('status', 'ready')" wire:loading.attr="disabled">Ready</button>
                <button type="button" class="btn @if ($status == 'completed' ) btn-success @else btn-outline-success @endif"
                    wire:click="$set('status', 'completed')" wire:loading.attr="disabled">Completed</button>
                <button type="button" class="btn @if ($status == 'cancelled' ) btn-danger @else btn-outline-danger @endif"
                    wire:click="$set('status', 'cancelled')" wire:loading.attr="disabled">Cancelled</button>
            </div>
        </div>
    </div>
    @if(count($selectedItems) > 0)
        @can('update orders')
            <p>
                <strong>Bulk change</strong> status of {{ count($selectedItems) }} orders to
                @php
                    $canReady = $orders->whereIn('id', $selectedItems)->whereNotIn('status', 'new')->isEmpty();
                    $canComplete = $orders->whereIn('id', $selectedItems)->whereNotIn('status', 'ready')->isEmpty();
                    $canCancel = $orders->whereIn('id', $selectedItems)->whereNotIn('status', ['new', 'ready'])->isEmpty();
                @endphp
                <button
                    type="button"
                    class="btn btn-sm ms-1 @if($canReady) btn-info @else btn-secondary @endif"
                    @unless($canReady) disabled @endunless
                    wire:click="bulkChange('ready')"
                    wire:target="bulkChange"
                    wire:loading.attr="disabled">
                    <x-spinner wire:loading wire:target="bulkChange('ready')"/>
                    Ready
                </button>
                <button
                    type="button"
                    class="btn btn-sm ms-1 @if($canComplete) btn-success @else btn-secondary @endif"
                    @unless($canComplete) disabled @endunless
                    wire:click="bulkChange('completed')"
                    wire:target="bulkChange"
                    wire:loading.attr="disabled">
                    <x-spinner wire:loading wire:target="bulkChange('completed')"/>
                    Completed
                </button>
                <button
                    type="button"
                    class="btn btn-sm ms-1 @if($canCancel) btn-danger @else btn-secondary @endif"
                    @unless($canCancel) disabled @endunless
                    wire:click="bulkChange('cancelled')"
                    wire:target="bulkChange"
                    wire:loading.attr="disabled">
                    <x-spinner wire:loading wire:target="bulkChange('cancelled')"/>
                    Cancelled
                </button>
            </p>
        @endcan
    @endif
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm table-hover">
            <caption>{{ $orders->total() }} orders found</caption>
            <thead>
                @can('update orders')
                    <th class="fit text-center">
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                value="{{ $orders->pluck('id')->join(',') }}"
                                wire:model="selectedAllItems">
                        </div>
                    </th>
                @endcan
                <th class="fit text-end">
                    ID
                    <a href="#" wire:click="sortBy('id')"><x-icon icon="sort"/></a>
                </th>
                <th class="fit">Status</th>
                <th>Customer</th>
                <th>Products</th>
                <th class="fit">
                    Registered
                    <a href="#" wire:click="sortBy('created_at')"><x-icon icon="sort"/></a>
                </th>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        @can('update orders')
                            <td class="fit text-center">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        value="{{ $order->id }}"
                                        wire:model="selectedItems">
                                </div>
                            </td>
                        @endcan
                        <td
                            @can('view', $order) onclick="window.location='{{ route('backend.orders.show', $order) }}'" @endcan
                            class="fit text-end @can('view', $order) cursor-pointer @endcan">
                            #{{ $order->id }}
                        </td>
                        <td
                            @can('view', $order) onclick="window.location='{{ route('backend.orders.show', $order) }}'" @endcan
                            class="fit @can('view', $order) cursor-pointer @endcan">
                            <x-order-status-label :order="$order" />
                        </td>
                        <td
                            @can('view', $order) onclick="window.location='{{ route('backend.orders.show', $order) }}'" @endcan
                            @can('view', $order) class="cursor-pointer" @endcan>
                            @isset($order->customer)
                                <strong>Name:</strong> {{ $order->customer->name }}<br>
                                <strong>ID Number:</strong> {{ $order->customer->id_number }}<br>
                                <strong>Phone:</strong> {{ $order->customer->phone }}
                            @else
                                <em>Deleted</em>
                            @endisset
                        </td>
                        <td
                            @can('view', $order) onclick="window.location='{{ route('backend.orders.show', $order) }}'" @endcan
                            @can('view', $order) class="cursor-pointer" @endcan>
                            @foreach ($order->products->sortBy('name') as $product)
                                <strong>{{ $product->pivot->quantity }}</strong> {{ $product->name }}<br>
                            @endforeach
                        </td>
                        <td
                            @can('view', $order) onclick="window.location='{{ route('backend.orders.show', $order) }}'" @endcan
                            class="fit @can('view', $order) cursor-pointer @endcan">
                            {{ $order->created_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                            <small>{{ $order->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>
                                @if (filled($search))
                                    No
                                    @isset($status)
                                        <strong>{{ $status }}</strong>
                                    @endisset
                                    orders found for term '{{ $search }}'.
                                @else
                                    No orders found.
                                @endif
                            </em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="overflow-auto">{{ $orders->onEachSide(2)->links() }}</div>
</div>
