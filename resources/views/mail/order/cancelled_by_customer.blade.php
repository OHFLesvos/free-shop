@component('mail::message')
# Shop order cancelled

The order #{{ $order->id }} has been cancelled by **{{ $order->customer->name }}**.

@component('mail::table')
| Item          | Quantity      |
| ------------- | -------------:|
@foreach($order->products->sortBy('name') as $product)
| {{ $product->name }} | {{  $product->pivot->quantity }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('backend.orders.show', $order)])
View details
@endcomponent

Update your notification settings <a href="{{ route('backend.user-profile') }}">here</a>.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
