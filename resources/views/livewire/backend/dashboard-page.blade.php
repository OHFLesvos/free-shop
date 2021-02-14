<div class="medium-container">
    @if(setting()->has('shop.disabled', false))
        <x-alert type="warning">The shop is currently disabled.</x-alert>
    @endif
    <div class="row row-cols-1 row-cols-md-2 gx-4 gy-2">
        <div class="col">
            <x-card>
                <x-slot name="title">
                    @canany(['view orders', 'manage orders'])
                        <a href="{{ route('backend.orders') }}" class="text-body text-decoration-none">Orders</a>
                    @else
                        Orders
                    @endcanany
                </x-slot>

                @if($newOrders > 0)
                    {{ $newOrders }} new orders
                    <br>
                @endif
                @if($readyOrders > 0)
                    {{ $readyOrders }} orders ready for pickup
                    <br>
                @endif
                @if($completedOrders > 0)
                    {{ $completedOrders }} orders completed
                @endif
            </x-card>
        </div>

        <div class="col">
            <x-card>
                <x-slot name="title">
                    @canany(['view customers', 'manage customers'])
                        <a href="{{ route('backend.customers') }}" class="text-body text-decoration-none">Customers</a>
                    @else
                        Customers
                    @endcanany
                </x-slot>

                {{ $registeredCustomers }} registered customers

            </x-card>
        </div>

        <div class="col">
            <x-card>
                <x-slot name="title">
                    <a href="{{ route('backend.products') }}" class="text-body text-decoration-none">Products</a>
                </x-slot>

                {{ $availableProducts }} products available
            </x-card>
        </div>

        <div class="col">
            <x-card>
                <x-slot name="title">
                    @can('manage users')
                        <a href="{{ route('backend.users') }}" class="text-body text-decoration-none">Users</a>
                    @else
                        Users
                    @endcan
                </x-slot>

                {{ $registeredUsers }} users registered
            </x-card>
        </div>
    </div>
</div>
