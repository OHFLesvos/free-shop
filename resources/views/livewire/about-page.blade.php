<div class="small-container">
    @isset($content)
        {!! $content !!}
    @endisset
    <h2>@lang('Statistics')</h2>
    @foreach($stats as $key => $values)
        <x-card :title="$key">
            In <strong>{{ $values['month_start']->isoFormat('MMMM YYYY') }}</strong> we completed 
            <strong>{{ number_format($values['ordersCompleted']) }} orders</strong> 
            for <strong>{{ number_format($values['customersWithCompletedOrders']) }} customers</strong>
            and handed out <strong>{{ number_format($values['totalProductsHandedOut']) }} products</strong> in total.
        </x-card>
    @endforeach
</div>
