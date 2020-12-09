<div>
    <h1 class="mb-3">Order #{{ $order->id }}</h1>
    <ul class="list-group mb-4 shadow-sm">
        <li class="list-group-item">
            <strong>Ordered:</strong> {{ $order->created_at->isoFormat('LLLL') }}
            <small class="ml-1">{{ $order->created_at->diffForHumans() }}</small>
            @isset($order->completed_at)<br>
                <strong>Completed:</strong> {{ $order->completed_at->isoFormat('LLLL') }}
                <small class="ml-1">{{ $order->completed_at->diffForHumans() }}</small>
            @endisset
        </li>
        <li class="list-group-item">
            <strong>Name:</strong> {{ $order->customer_name }}<br>
            <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
            <strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
        </li>
        <li class="list-group-item">
            <strong>IP Address:</strong> {{ $order->customer_ip_address }}
            @if(!$order->geoIpLocation->default)
            ({{ $order->geoIpLocation->city }}, @isset($order->geoIpLocation->state){{ $order->geoIpLocation->state }},@endisset {{ $order->geoIpLocation->country }})
            @endif
            <br>
            <strong>User Agent:</strong> {{ $order->UA->browser() }} {{ $order->UA->browserVersion() }} on {{ $order->UA->platform() }}<br>
        </li>
        @isset($order->remarks)
            <li class="list-group-item">
                <strong>Remarks:</strong><br>{!! nl2br(e($order->remarks)) !!}
            </li>
        @endisset
    </ul>
    <table class="table table-bordered shadow-sm mb-4">
        <tbody>
            @foreach($order->products as $product)
                <tr>
                    <td class="fit"><img src="{{ $product->imageUrl(100, 75) }}" alt="Product Image"/></td>
                    <td>{{ $product->name }}<br><small>{{ $product->category }}</small></td>
                    <td class="fit text-right"><strong><big>{{ $product->pivot->amount }}</big></strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-between mb-3">
        <a
            href="{{ route('backend.orders') }}"
            class="btn btn-outline-primary">Back to orders</a>
        @isset($order->completed_at)
            Completed
        @else
            <button
                class="btn btn-primary"
                wire:click="complete"
                wire:loading.attr="disabled">
                <x-bi-hourglass-split wire:loading/>
                Mark as completed
            </button>
        @endisset
    </div>
</div>
