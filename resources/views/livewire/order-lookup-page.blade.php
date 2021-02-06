<div>
    @forelse($orders as $order)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                {{ $order->created_at->toUserTimezone()->isoFormat('LLLL') }}
            </div>
            <table class="table table-bordered m-0">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell"></th>
                        <th>@lang('Product')</th>
                        <th class="fit">@lang('Quantity')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                    @endphp
                    @foreach($order->products->sortBy('name') as $product)
                        <tr>
                            @if($hasPictures)
                                <td class="fit d-none d-md-table-cell">
                                    @isset($product->pictureUrl)
                                        <img
                                            src="{{ $product->pictureUrl }}"
                                            alt="Product Image"
                                            style="max-width: 100px; max-height: 75px"/>
                                    @endisset
                                </td>
                            @endif
                            <td>
                                {{ $product->name }}<br>
                                <small>{{ $product->category }}</small>
                            </td>
                            <td class="fit text-end align-middle">
                                <strong><big>{{ $product->pivot->quantity }}</big></strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="card-footer">
                @if($order->status == 'cancelled')
                    <x-icon icon="ban"/>
                    @lang('This order has been cancelled.')
                @elseif($order->status == 'completed')
                    <x-icon icon="check-circle" type="regular" />
                    @lang('This order has been completed.')
                @elseif($order->status == 'ready')
                    <x-icon icon="box" />
                    @lang('This order is ready.')
                @else
                    <x-icon icon="inbox"/>
                    @lang('This order is in progress.')
                @endif
            </div>
        </div>
    @empty
        <x-alert type="info">@lang('No orders found.')</x-alert>
    @endforelse
</div>
