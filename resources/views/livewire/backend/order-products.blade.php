<div>
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm">
            @php
                $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
            @endphp
            <thead>
                <tr>
                    <th @if ($hasPictures) colspan="2" @endif>Product</th>
                    <th class="text-center">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products->sortBy('name') as $product)
                    <tr>
                        @if ($hasPictures)
                            <td class="fit">
                                @isset($product->pictureUrl)
                                    <img
                                        src="{{ url($product->pictureUrl) }}"
                                        alt="Product Image"
                                        style="max-width: 100px; max-height: 75px" />
                                @endisset
                            </td>
                        @endif
                        <td>
                            {{ $product->name }}<br>
                            <small class="text-muted">{{ $product->category }}</small>
                        </td>
                        <td class="fit text-center align-middle">
                            <strong><big>{{ $product->pivot->quantity }}</big></strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <div  class="d-flex justify-content-between">
                            <strong>Total costs:</strong>
                            {{ $order->getCostsString() }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
