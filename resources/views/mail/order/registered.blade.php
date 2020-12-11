@component('mail::message')
# New order in the shop

A new order has been registered by **{{ $order->customer_name }}**.

@component('mail::table')
| Item          | Amount        |
| ------------- | -------------:|
@foreach($order->products as $product)
| {{ $product->name }} | {{  $product->pivot->amount }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('backend.orders.show', $order)])
View details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
