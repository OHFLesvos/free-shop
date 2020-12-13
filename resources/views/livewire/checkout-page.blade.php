<div>
    @if(isset($order))
        <x-alert type="success">
            @lang('Your order has been submitted and your order number is <strong>#:id</strong>.', ['id' => $order->id])<br>
            @lang('We will contact you via your phone <strong>:phone</strong> when the order is ready.', ['phone' => $order->customer_phone])
        </x-alert>
    @elseif ($basket->isNotEmpty())
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">@lang('Selected products')</div>
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th>@lang('Item')</th>
                            <th class="text-right">@lang('Quantity')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Product::whereIn('id', $basket->keys())->get()->sortBy('name') as $product)
                            <tr>
                                <td>
                                    {{ $product->name }}
                                    <small class="text-muted ml-1">{{ $product->category }}</small>
                                </td>
                                <td class="text-right align-middle">
                                    <strong>{{ $basket[$product->id] }}</strong>
                                </td>
                            </tr>
                            {{-- <div class="input-group justify-content-end">
                            <input
                                type="number"
                                wire:model.lazy="basket.{{ $product->id }}"
                                value="{{ $basket[$product->id] ?? 0 }}"
                                min="0"
                                max="{{ $product->quantity_available_for_customer }}"
                                style="max-width: 7em"
                                class="form-control text-center @error('basket.'.$product->id) is-invalid @enderror"
                                placeholder="Quantity">
                        </div> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card mb-4 shadow-sm">
                <div class="card-header">@lang('Contact data')</div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerName">@lang('First & last name')</label>
                                <input
                                    type="text"
                                    class="form-control @error('customer_name') is-invalid @enderror"
                                    id="inputCustomerName"
                                    wire:model.defer="customer_name"
                                    required
                                    autocomplete="off"
                                    aria-describedby="customerNameHelp">
                                @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="customerNameHelp" class="form-text text-muted">
                                    @lang('Write your full name according to your identification document.')
                                </small>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerIdNumber">@lang('ID number')</label>
                                <input
                                    type="text"
                                    class="form-control @error('customer_id_number') is-invalid @enderror"
                                    id="inputCustomerIdNumber"
                                    wire:model.defer="customer_id_number"
                                    required
                                    autocomplete="off"
                                    aria-describedby="customerIdNumberHelp">
                                @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="customerIdNumberHelp" class="form-text text-muted">
                                    @lang('Write your ID number according to your identification document.')
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputCustomerPhone">@lang('Mobile phone number')</label>
                                <div class="input-group">
                                    <select class="custom-select" style="max-width: 10em;" wire:model.defer="customer_phone_country">
                                        @isset($countries)
                                            @foreach($countries as $key => $val)
                                                <option value="{{ $key }}">{{ $val }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input
                                        type="tel"
                                        class="form-control @error('customer_phone') is-invalid @enderror"
                                        id="inputCustomerPhone"
                                        wire:model.defer="customer_phone"
                                        required
                                        autocomplete="off"
                                        aria-describedby="customerPhoneHelp">
                                    @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <small id="customerPhoneHelp" class="form-text text-muted">
                                    @lang('We will send updates about your order to this number.')
                                </small>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="inputRemarks">@lang('Remarks')</label>
                                <textarea
                                    class="form-control @error('remarks') is-invalid @enderror"
                                    id="inputRemarks"
                                    wire:model.defer="remarks"
                                    rows="3"
                                    autocomplete="off"
                                    aria-describedby="remarksHelp"></textarea>
                                @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small id="remarksHelp" class="form-text text-muted">
                                    @lang('Please write if we need to know anything more regarding your order.')
                                </small>
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
                    @lang('Restart')
                </button>
                <button
                    type="submit"
                    class="btn btn-primary">
                    {{-- class="btn @error('basket.*') btn-secondary @else btn-primary @enderror" --}}
                    {{-- @error('basket.*') disabled @enderror --}}
                    <x-icon-progress wire:loading wire:target="submit"/>
                    @lang('Send order')
                </button>
            </div>
        </form>
    @else
        <x-alert type="warning">
            @lang('No items selected.')
        </x-alert>
        <p>
            <a href="{{ route('shop-front') }}" class="btn btn-primary">@lang('Choose items')</a>
        </p>
    @endif
</div>
