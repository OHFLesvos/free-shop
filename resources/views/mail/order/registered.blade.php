@component('mail::message')
# New order in the shop

A new order has been registered by **{{ $order->customer->name }}**.

@component('mail::table')
| Item          | Quantity      |
| ------------- | -------------:|
@foreach($order->products->sortBy('name') as $product)
| {{ $product->name }} | {{  $product->pivot->quantity }} |
@endforeach
@endcomponent

@isset($order->remarks)
@component('mail::panel')
Remarks: {{ $order->remarks }}
@endcomponent
@endisset

@component('mail::button', ['url' => route('backend.orders.show', $order)])
View details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
