@component('mail::message')
# {{ __('Your order is ready') }}

{{ message }}

@component('mail::button', ['url' => $url])
{{ __('Your orders') }}
@endcomponent

@endcomponent
