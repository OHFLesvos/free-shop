<div>
    <p>Welcome to our FREE shop. Please place an order from out selection of items:</p>

    <h2>Items</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
        @foreach($products as $product)
            <div class="col mb-4">
                <div class="card">
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

    <h2>Customer</h2>
    <form wire:submit.prevent="submit" class="mb-4">
        <div class="form-group">
            <label for="inputCustomerName">First & last name</label>
            <input
                type="text"
                class="form-control @error('order.customer_name') is-invalid @enderror"
                id="inputCustomerName"
                wire:model.defer="order.customer_name"
                placeholder="John Doe"
                aria-describedby="customerNameHelp">
            @error('order.customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small id="customerNameHelp" class="form-text text-muted">Write your full name according to your identification document.</small>
        </div>
        <div class="form-group">
            <label for="inputCustomerIdNumber">ID number</label>
            <input
                type="text"
                class="form-control @error('order.customer_id_number') is-invalid @enderror"
                id="inputCustomerIdNumber"
                wire:model.defer="order.customer_id_number"
                placeholder="05/0123456789"
                aria-describedby="customerIdNumberHelp">
            @error('order.customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small id="customerIdNumberHelp" class="form-text text-muted">Write your ID number according to your identification document.</small>
        </div>
        <div class="form-group">
            <label for="inputCustomerPhone">Phone number</label>
            <input
                type="tel"
                class="form-control @error('order.customer_phone') is-invalid @enderror"
                id="inputCustomerPhone"
                wire:model.defer="order.customer_phone"
                placeholder="+30 123 456 78 90"
                aria-describedby="customerPhoneHelp">
            @error('order.customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small id="customerPhoneHelp" class="form-text text-muted">We will send you updates about your order to this number.</small>
        </div>
        <div class="form-group">
            <label for="inputRemarks">Remarks</label>
            <textarea
                class="form-control @error('order.remarks') is-invalid @enderror"
                id="inputRemarks"
                wire:model.defer="order.remarks"
                rows="3"
                placeholder="Your remarks"
                aria-describedby="remarksHelp"></textarea>
            @error('order.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small id="remarksHelp" class="form-text text-muted">Please write if we need to know anything more regarding your order.</small>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
