@component('mail::message')
# {{ $title }}

{{ $message }}

@component('mail::button', ['url' => $url])
{{ __('Your orders') }}
@endcomponent

@component('mail::table')
| {{ __('Product') }} | {{ __('Quantity') }} |
| ------------------- | --------------------:|
@foreach($products as $product)
| {{ $product->name }} | {{ $product->pivot->quantity }} |
@endforeach
@endcomponent

@endcomponent
