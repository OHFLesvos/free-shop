<div class="small-container">
    <h1>{{ __('Statistics') }}</h1>
    <div class="row row-cols-1 row-cols-md-2 gx-4 gy-2">
    @foreach($stats as $key => $values)
    <div class="col">
        <x-card :title="$key">
            {!! __('In <strong>:month</strong> we completed <strong>:orders orders</strong> for <strong>:customers customers</strong> and handed out <strong>:products products</strong> in total.', [
                'month' => $values['month_start']->isoFormat('MMMM YYYY'),
                'orders' => number_format($values['ordersCompleted']),
                'customers' => number_format($values['customersWithCompletedOrders']),
                'products' => number_format($values['totalProductsHandedOut']),
            ]) !!}
            @if($values['totalProductsHandedOut'] > 0)
                {!! __('<strong>:days days</strong> were needed on average to complete an order.', [
                    'days' => round($values['averageOrderDuration'], 1),
                ]) !!}
            @endif
            @if($values['productsHandedOut']->isNotEmpty())
                <x-slot name="addon">
                    <table class="table mb-0">
                        <thead>
                            <th>{{ __('Product') }}</th>
                            <th class="fit text-end">{{ __('Quantity') }}</th>
                        </thead>
                        <tbody>
                            @foreach($values['productsHandedOut'] as $product)
                                <tr>
                                    <td>{{ $product['name'] }}</td>
                                    <td class="fit text-end">
                                        {{ number_format($product['quantity']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-slot>
            @endif
        </x-card>
    </div>
    @endforeach
</div>
