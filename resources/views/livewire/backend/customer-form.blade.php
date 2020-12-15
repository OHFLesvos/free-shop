<div>
    @if($shouldDelete)
        <h1 class="mb-3">Delete Customer</h1>
        <p>Really delete the customer <strong>{{ $customer->name }}</strong>?</p>
        <p class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldDelete')">
                Cancel
            </button>
            <button
                type="button"
                class="btn btn-outline-danger"
                wire:target="delete"
                wire:loading.attr="disabled"
                wire:click="delete">
                <x-spinner wire:loading wire:target="delete"/>
                Delete
            </button>
        </p>
    @else
        <h1 class="mb-3">
            @if($customer->exists)
                Edit Customer
            @else
                Register Customer
            @endif
        </h1>
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <div class="form-row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <input
                            type="text"
                            class="form-control @error('customer.name') is-invalid @enderror"
                            id="inputName"
                            required
                            @unless($customer->exists) autofocus @endunless
                            autocomplete="off"
                            wire:model.defer="customer.name">
                        @error('customer.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputIdNumber">ID Number</label>
                        <input
                            type="text"
                            class="form-control @error('customer.id_number') is-invalid @enderror"
                            id="inputIdNumber"
                            required
                            autocomplete="off"
                            wire:model.defer="customer.id_number">
                        @error('customer.id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputPhone">Phone</label>
                        <div class="input-group">
                            @php
                                $codes = megastruktur\PhoneCountryCodes::getCodesList();
                            @endphp
                            <select
                                class="custom-select"
                                style="max-width: 11em;"
                                wire:model.defer="customer_phone_country">
                                <option value="" selected>-- Select country --</option>
                                @foreach(Countries::getList() as $key => $val)
                                    <option value="{{ $key }}">
                                        {{ $val }}
                                        @isset($codes[$key] )({{ $codes[$key] }})@endisset
                                    </option>
                                @endforeach
                            </select>
                            <input
                                type="tel"
                                class="form-control @error('customer_phone') is-invalid @enderror"
                                id="inputPhone"
                                required
                                autocomplete="off"
                                wire:model.defer="customer_phone">
                            @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputRemarks">Remarks</label>
                        <textarea
                            class="form-control @error('customer.remarks') is-invalid @enderror"
                            id="inputRemarks"
                            autocomplete="off"
                            rows="3"
                            wire:model.defer="customer.remarks"></textarea>
                        @error('customer.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                @if($customer->exists)
                    <a
                        href="{{ route('backend.customers.show', $customer) }}"
                        class="btn btn-outline-primary">Back to customer</a>
                @else
                    <a
                        href="{{ route('backend.customers') }}"
                        class="btn btn-outline-primary">Back to customers</a>
                @endif
                <span>
                    @if($customer->exists)
                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            wire:loading.attr="disabled"
                            wire:click="$toggle('shouldDelete')">
                            Delete
                        </button>
                    @endif
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:target="submit"
                        wire:loading.attr="disabled">
                        <x-spinner wire:loading wire:target="submit"/>
                        Save
                    </button>
                </span>
            </div>
        </form>
    @endif
</div>
