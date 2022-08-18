<div class="container">
    @can('update settings')
        @if(setting()->has('shop.disabled', false))
            <x-alert type="warning">
                The shop is currently disabled.
                <a href="{{ route('backend.configuration.settings') }}" class="float-end">Change</a>
            </x-alert>
        @endif
        @if(!filled(setting()->get('customer.credit_top_up.days')))
        <x-alert type="info" class="mb-4">
                Automatic top-up is not enabled.
                <a href="{{ route('backend.configuration.settings') }}" class="float-end">Change</a>
            </x-alert>
        @endif
    @endcan

    @if(Auth::user()->roles->isEmpty() && Auth::user()->permissions->isEmpty())
        <x-alert type="warning" class="mb-4">
            You don't seem to have any permissions yet.<br>
            Please ask an administrator to assign you one or more roles.
        </x-alert>
    @endif

    <div class="row" data-masonry='{"percentPosition": true }'>
        @php ob_start() @endphp
        <x-backend.dashboard.orders-widget/>
        <x-backend.dashboard.customers-widget/>
        <x-backend.dashboard.products-widget/>
        <x-backend.dashboard.users-widget/>
        <x-backend.dashboard.twilio-widget/>
        <x-backend.dashboard.stock-change-widget/>
        <x-backend.dashboard.system-info-widget/>
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
