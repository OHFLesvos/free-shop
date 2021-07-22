@component('mail::message')
# {{ __('Your order has been registered') }}

{{ message }}

@component('mail::button', ['url' => $url])
{{ __('Your orders') }}
@endcomponent

@endcomponent
