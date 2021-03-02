@can('viewAny', App\Models\Order::class)
    <div class="col">
        <x-card>
            <x-slot name="title">
                <a href="{{ route('backend.orders') }}" class="text-body text-decoration-none">Orders</a>
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
@endcan