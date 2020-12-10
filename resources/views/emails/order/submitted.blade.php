@component('mail::message')
# New order in the shop

A new order has been registered in the shop.

@component('mail::button', ['url' => route('backend.orders.show', $order)])
View details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
