<div>
    <p>Welcome to our FREE shop. Please place an order from out selection of items:</p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
        @foreach($products as $product)
            <div class="col mb-4">
                <div class="card shadow-sm">
                    <img src="{{ $product->imageUrl(300, 150) }}" class="card-img-top" alt="Product name">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text"><small class="text-muted">{{ $product->category }}</small></p>
                        <p class="card-text">{{ $product->description }}</p>
                        @if($product->available_for_customer_amount > 0)
                            <p class="card-text"><small class="text-muted">
                                Available: {{ $product->available_for_customer_amount }}
                            </small></p>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if($product->available_for_customer_amount > 0)
                            <div class="input-group justify-content-end">
                                <input
                                    type="number"
                                    value="0"
                                    min="0"
                                    max="{{ $product->available_for_customer_amount }}"
                                    style="max-width: 5em"
                                    class="form-control"
                                    placeholder="Amount">
                                <div class="input-group-append">
                                    <button
                                        class="btn btn-primary"
                                        type="button">Add</button>
                                </div>
                            </div>
                        @else
                            <div class="text-right text-danger"><small>Not available</small></div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
