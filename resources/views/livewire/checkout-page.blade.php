<div>
    @if($submitted)
        <x-alert type="success">
            Your order has been submitted and your order number is <strong>#{{ $order->id }}</strong>.<br>
            We will contact you via your phone <strong>{{ $order->customer_phone }}</strong> when the order is ready.
        </x-alert>
    @else
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">Selected products</div>
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->basketContents as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td><strong>{{ $item['amount'] }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">Contact data</div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerName">First & last name</label>
                                <input
                                    type="text"
                                    class="form-control @error('order.customer_name') is-invalid @enderror"
                                    id="inputCustomerName"
                                    wire:model.defer="order.customer_name"
                                    required
                                    autocomplete="off"
                                    aria-describedby="customerNameHelp">
                                @error('order.customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="customerNameHelp" class="form-text text-muted">Write your full name according to your identification document.</small>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerIdNumber">ID number</label>
                                <input
                                    type="text"
                                    class="form-control @error('order.customer_id_number') is-invalid @enderror"
                                    id="inputCustomerIdNumber"
                                    wire:model.defer="order.customer_id_number"
                                    required
                                    autocomplete="off"
                                    aria-describedby="customerIdNumberHelp">
                                @error('order.customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="customerIdNumberHelp" class="form-text text-muted">Write your ID number according to your identification document.</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerPhone">Mobile phone number</label>
                                <input
                                    type="tel"
                                    class="form-control @error('order.customer_phone') is-invalid @enderror"
                                    id="inputCustomerPhone"
                                    wire:model.defer="order.customer_phone"
                                    required
                                    autocomplete="off"
                                    aria-describedby="customerPhoneHelp">
                                @error('order.customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="customerPhoneHelp" class="form-text text-muted">We will send updates about your order to this number.</small>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputRemarks">Remarks</label>
                                <textarea
                                    class="form-control @error('order.remarks') is-invalid @enderror"
                                    id="inputRemarks"
                                    wire:model.defer="order.remarks"
                                    rows="3"
                                    autocomplete="off"
                                    aria-describedby="remarksHelp"></textarea>
                                @error('order.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="remarksHelp" class="form-text text-muted">Please write if we need to know anything more regarding your order.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    wire:click="restart">
                    Restart
                </button>
                <button
                    type="submit"
                    class="btn btn-primary">
                    <x-bi-hourglass-split wire:loading wire:target="submit"/>
                    Send order
                </button>
            </div>
        </form>
    @endif
</div>
