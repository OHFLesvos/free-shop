<div class="small-container">
    @forelse($orders as $order)
        <x-card :title="__('Order #:id', ['id' => $order->id])">
            <p class="card-text">
                @lang('Ordered on :date', ['date' => $order->created_at->toUserTimezone()->isoFormat('LLLL')])
            </p>
            <ul>
                @foreach($order->products->sortBy('name') as $product)
                    <li>
                        <strong>{{ $product->pivot->quantity }}</strong>
                        {{ $product->name }}
                        <small class="text-muted ms-2">{{ $product->category }}</small>
                    </li>
                @endforeach
            </ul>
            <p class="card-text">
                @if($order->status == 'cancelled')
                    <x-icon icon="ban" class="text-danger" />
                    @lang('This order has been cancelled.')
                @elseif($order->status == 'completed')
                    <x-icon icon="check-circle" type="regular" class="text-success" />
                    @lang('This order has been completed.')
                @elseif($order->status == 'ready')
                    <x-icon icon="box" class="text-info" />
                    @lang('This order is ready.')
                @else
                    <x-icon icon="inbox" class="text-warning"/>
                    @lang('This order is in progress.')
                @endif
            </p>
            @if($order->status == 'new')
                <x-slot name="footer">
                    <div class="row g-2 align-items-center">
                        @if($requestCancel == $order->id)
                            <div class="col-md">
                                @lang('Do you really want to cancel your order?')
                            </div>
                            <div class="col-auto">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    wire:click="cancelOrder({{ $order->id }})"
                                    wire:loading.attr="disabled">
                                    <x-spinner wire:loading wire:target="cancelOrder"/>
                                    @lang('Yes')
                                </button>
                            </div>
                            <div class="col-auto">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    wire:click="$set('requestCancel', 0)"
                                    wire:loading.attr="disabled"
                                    wire:loading.remove wire:target="cancelOrder">
                                    @lang('No, keep my order')
                                </button>
                            </div>
                        @else
                            <div class="col text-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger"
                                    wire:click="$set('requestCancel', {{ $order->id }})"
                                    wire:loading.attr="disabled">
                                    @lang('Cancel order')
                                </button>
                            </div>
                        @endif
                    </div>
                </x-slot>
            @endif
        </x-card>
    @empty
        <x-alert type="info">@lang('No orders found.')</x-alert>
    @endforelse
</div>
