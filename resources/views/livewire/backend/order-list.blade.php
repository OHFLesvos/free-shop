<div>
    <h1>Orders</h1>
    <table class="table">
        <thead>
            <th>Date</th>
            <th>Customer</th>
            <th>Remarks</th>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>
                        {{ $order->created_at->isoFormat('LLLL') }}<br>
                        <small>{{ $order->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <strong>Name:</strong> {{ $order->customer_name }}<br>
                        <strong>ID Number:</strong> {{ $order->customer_id_number }}<br>
                        <strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                    </td>
                    <td style="max-width: 20em">{{ $order->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
