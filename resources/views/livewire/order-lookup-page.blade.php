<div>
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">Find your order</div>
            <div class="card-body">
                <div class="form-row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="inputCustomerIdNumber">Your ID number</label>
                            <input
                                type="text"
                                class="form-control @error('customer_id_number') is-invalid @enderror"
                                id="inputCustomerIdNumber"
                                wire:model.defer="customer_id_number"
                                required
                                autofocus
                                autocomplete="off">
                            @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for="inputCustomerPhone">Your phone number</label>
                            <input
                                type="tel"
                                class="form-control @error('customer_phone') is-invalid @enderror"
                                id="inputCustomerPhone"
                                wire:model.defer="customer_phone"
                                required
                                autocomplete="off">
                            @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <button
                type="submit"
                class="btn btn-primary">
                <x-bi-hourglass-split wire:loading wire:target="submit"/>
                Search
            </button>
        </div>
    </form>
    @if($results !== null)
        @forelse($results as $order)
            <h4>{{ $order->created_at->isoFormat('LLLL') }}</h4>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    Order for {{ $order->customer_name }}
                </div>
                <table class="table table-bordered m-0">
                    <tbody>
                        @php
                            $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                        @endphp
                        @foreach($order->products as $product)
                            <tr>
                                @if($hasPictures)
                                    <td class="fit">
                                        @isset($product->pictureUrl)
                                            <img
                                                src="{{ $product->pictureUrl }}"
                                                alt="Product Image"
                                                style="max-width: 100px; max-height: 75px"/>
                                        @endisset
                                    </td>
                                @endif
                                <td>
                                    {{ $product->name }}<br>
                                    <small>{{ $product->category }}</small>
                                </td>
                                <td class="fit text-right">
                                    <strong><big>{{ $product->pivot->amount }}</big></strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @isset($order->remarks)
                    <div class="card-body">
                        <strong>Your remarks:</strong> {!! nl2br(e($order->remarks)) !!}
                    </div>
                @endisset
                <div class="card-footer">
                    @if($order->cancelled_at !== null)
                        This order has been <span class="text-danger">cancelled</span> on {{ $order->cancelled_at->isoFormat('LLLL') }}.
                    @elseif($order->completed_at !== null)
                        This order has been <span class="text-success">completed</span> on {{ $order->completed_at->isoFormat('LLLL') }}.
                    @else
                        This order is still <span class="text-info">open.</span>
                    @endif
                </div>
            </div>
        @empty
            <x-alert type="info">No orders found.</x-alert>
        @endforelse
    @endif
</div>
