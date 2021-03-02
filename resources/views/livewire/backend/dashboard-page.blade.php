<div class="medium-container">
    @if(setting()->has('shop.disabled', false))
        <x-alert type="warning">The shop is currently disabled.</x-alert>
    @endif
    @if(Auth::user()->roles->isEmpty() && Auth::user()->permissions->isEmpty())
        <x-alert type="warning" class="mb-4">
            You don't seem to have any permissions yet.<br>
            Please ask an administrator to assign you one or more roles.
        </x-alert>
    @endif
    <div class="row row-cols-1 row-cols-md-2 gx-4 gy-2">
        @php ob_start() @endphp
        <x-backend.dashboard.orders-widget/>
        <x-backend.dashboard.customers-widget/>
        <x-backend.dashboard.products-widget/>
        <x-backend.dashboard.users-widget/>
        <x-backend.dashboard.twilio-widget/>
        @php
            $content = ob_get_clean();
        @endphp
        @if(filled($content))
            {!! $content !!}
        @endif
    </div>
    @unless(filled($content))
        <x-alert type="info">No content available for you at the moment.</x-alert>
    @endunless
</div>
