<div class="medium-container">
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
        </x-card>
    @empty
        <x-alert type="info">@lang('No orders found.')</x-alert>
    @endforelse
</div>
