<div class="medium-container">

    {{-- Order details --}}
    <x-card title="Order #{{ $order->id }}" no-footer-padding>
        <dl class="row mb-2 mt-3">
            <dt class="col-sm-3">Customer</dt>
            <dd class="col-sm-9">
                @isset($order->customer)
                    {{ $order->customer->name }}, {{ $order->customer->id_number }}
                @else
                    <em>Deleted</em>
                @endisset
            </dd>
            <dt class="col-sm-3">Registered</dt>
            <dd class="col-sm-9"><x-date-time-info :value="$order->created_at" /></dd>
            @isset($order->remarks)
                <dt class="col-sm-3">Remarks from customer</dt>
                <dd class="col-sm-9">{!! nl2br(e($order->remarks)) !!}</dd>
            @endisset
        </dl>
    </x-card>

    <form wire:submit.prevent="submit" autocomplete="off">

        {{-- Products --}}
        <div class="table-responsive">
            <table class="table table-bordered bg-white shadow-sm">
                @php
                    $hasPictures = $order->products->whereNotNull('pictureUrl')->isNotEmpty();
                @endphp
                <thead>
                    <tr>
                        <th @if ($hasPictures) colspan="2" @endif>Product</th>
                        <th class="fit text-end">Costs</th>
                        <th class="fit">Quantity</th>
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
                            <td class="fit align-middle text-end">
                                {{ $product->price }}
                            </td>
                            <td class="fit align-middle">
                                <input
                                    type="number"
                                    placeholder="0"
                                    min="0"
                                    class="form-control
                                    @error('selection') is-invalid @enderror
                                    @error('selection.'.$product->id) is-invalid @enderror
                                    "
                                    style="width: 6em"
                                    autocomplete="off"
                                    wire:model="selection.{{ $product->id }}">
                                    @error('selection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @error('selection.'.$product->id) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Buttons --}}
        <div class="d-flex justify-content-between mb-3">
            <span>
                <button
                    type="submit"
                    class="btn btn-primary">
                    Update
                </button>
            </span>
            <a
                href="{{ route('backend.orders.show', $order) }}"
                class="btn btn-link">
                Cancel
            </a>
        </div>

    </form>
</div>
