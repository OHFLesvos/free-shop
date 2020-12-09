<div>
    <h1 class="mb-3">Order #{{ $order->id }}</h1>
    <ul class="list-group mb-4 shadow-sm">
        <li class="list-group-item">
            <strong>Date:</strong> {{ $order->created_at->isoFormat('LLLL') }}<br>
            <small>{{ $order->created_at->diffForHumans() }}</small>
        </li>
        <li class="list-group-item">
            <strong>Name:</strong> {{ $order->customer_name }}<br>
            <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
            <strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
        </li>
        <li class="list-group-item">
            <strong>IP Address:</strong> {{ $order->customer_ip_address }}<br>
            <strong>User Agent:</strong> {{ $order->customer_user_agent }}<br>
        </li>
        @isset($remarks)
            <li class="list-group-item">
                <strong>Remarks:</strong> {{ $order->remarks }}
            </li>
        @endisset
    </ul>
    <table class="table table-bordered shadow-sm mb-4">
        <tbody>
            @foreach($order->products as $product)
                <tr>
                    <td class="fit"><img src="{{ $product->imageUrl(100, 75) }}" alt="Product Image"/></td>
                    <td>{{ $product->name }}<br><small>{{ $product->category }}</small></td>
                    <td class="fit text-right"><strong>{{ $product->pivot->amount }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between mb-3">
        <a
            href="{{ route('backend.orders') }}"
            class="btn btn-outline-primary">Back to overview</a>
        @isset($order->delivered_at)
            Completed on {{ $order->delivered_at->isoFormat('LLLL') }}
        @else
            <button
                class="btn btn-primary"
                wire:click="complete">Mark as completed</button>
        @endisset
    </div>
</div>
