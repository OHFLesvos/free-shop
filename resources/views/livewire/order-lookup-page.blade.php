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
            @if(in_array($order->status, ['new', 'ready']))
            <x-slot name="footer">
                <div class="d-flex justify-content-end align-items-center">
                    @if($requestCancel == $order->id)
                        <span class="me-2">
                            @lang('Do you really want to cancel your order?')
                        </span>
                        <span>
                            <button
                                type="button"
                                class="btn btn-danger"
                                wire:click="cancelOrder({{ $order->id }})"
                                wire:loading.attr="disabled">
                                @lang('Yes')
                            </button>
                            <button
                                type="button"
                                class="btn btn-secondary"
                                wire:click="$set('requestCancel', 0)">
                                @lang('No, keep my order')
                            </button>
                        </span>
                    @else
                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            wire:click="$set('requestCancel', {{ $order->id }})">
                            @lang('Cancel order')
                        </button>
                    @endif
                </div>
            </x-slot>
            @endif
        </x-card>
    @empty
        <x-alert type="info">@lang('No orders found.')</x-alert>
    @endforelse
</div>
