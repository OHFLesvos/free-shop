<div class="medium-container">
    @if(setting()->has('shop.disabled', false))
        <x-alert type="warning">The shop is currently disabled.</x-alert>
    @endif
    @if(Auth::user()->roles->isEmpty() && Auth::user()->permissions->isEmpty())
        <x-alert type="warning">
            You don't seem to have any permissions yet.<br>
            Please ask an administrator to assign you one or more roles.
        </x-alert>
    @endif
    <div class="row row-cols-1 row-cols-md-2 gx-4 gy-2">
        <x-backend.dashboard.orders-widget/>
        <x-backend.dashboard.customers-widget/>
        <x-backend.dashboard.products-widget/>
        <x-backend.dashboard.users-widget/>
        <x-backend.dashboard.twilio-widget/>
    </div>
</div>
