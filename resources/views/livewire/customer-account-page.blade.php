<div>
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        @if (session()->has('message'))
            <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
        @endif
        <div class="row">
            <div class="col-sm-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">@lang('Customer Profile')</div>
                    <div class="card-body">
                        {{-- Name --}}
                        <div class="form-group">
                            <label for="inputCustomerName">@lang('First & last name')</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                id="inputCustomerName" wire:model.defer="customer_name" required autocomplete="off"
                                aria-describedby="customerNameHelp">
                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small id="customerNameHelp" class="form-text text-muted">
                                @lang('Write your full name according to your identification document.')
                            </small>
                        </div>
                        {{-- ID number --}}
                        <div class="form-group">
                            <label for="inputCustomerIdNumber">@lang('ID number')</label>
                            <input type="text" class="form-control @error('customer_id_number') is-invalid @enderror"
                                id="inputCustomerIdNumber" wire:model.defer="customer_id_number" required
                                autocomplete="off" aria-describedby="customerIdNumberHelp">
                            @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small id="customerIdNumberHelp" class="form-text text-muted">
                                @lang('Write your ID number according to your identification document.')
                            </small>
                        </div>
                        {{-- Phone number --}}
                        <div class="form-group">
                            <label for="inputCustomerPhone">@lang('Mobile phone number')</label>
                            <div class="input-group">
                                @php
                                $codes = megastruktur\PhoneCountryCodes::getCodesList();
                                @endphp
                                <select class="custom-select" style="max-width: 11em;"
                                    wire:model.defer="customer_phone_country">
                                    @foreach(collect(Countries::getList(app()->getLocale())) as $key => $val)
                                    <option value="{{ $key }}">
                                        {{ $val }}
                                        @isset($codes[$key])({{ $codes[$key] }})@endisset
                                    </option>
                                    @endforeach
                                </select>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                    id="inputCustomerPhone" wire:model.defer="customer_phone" required
                                    autocomplete="off" aria-describedby="customerPhoneHelp">
                                @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small id="customerPhoneHelp" class="form-text text-muted">
                                @lang('We will send updates about your order to this number.')
                            </small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        {{-- Logout --}}
                        <a href="{{ route('customer.logout') }}" class="btn btn-link text-danger">
                            @lang('Logout')
                        </a>
                        {{-- Save --}}
                        <button type="submit" class="btn btn-primary">
                            <x-spinner wire:loading wire:target="submit" />
                            @lang('Save')
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">@lang('Credit')</div>
                    <div class="card-body">
                        <big>@lang(':amount points', ['amount' => $customer->credit])</big>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
