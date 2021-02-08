<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

        <div class="row">
            <div class="col-sm-8">

                <x-card :title="__('Customer Profile')">
                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="inputCustomerName" class="form-label">@lang('First & last name')</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                            id="inputCustomerName" wire:model.defer="customer_name" required autocomplete="off"
                            aria-describedby="customerNameHelp">
                        @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="customerNameHelp" class="form-text text-muted">
                            @lang('Write your full name according to your identification document.')
                        </small>
                    </div>

                    {{-- ID number --}}
                    <div class="mb-3">
                        <label for="inputCustomerIdNumber" class="form-label">@lang('ID number')</label>
                        <input type="text" class="form-control @error('customer_id_number') is-invalid @enderror"
                            id="inputCustomerIdNumber" wire:model.defer="customer_id_number" required
                            autocomplete="off" aria-describedby="customerIdNumberHelp">
                        @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="customerIdNumberHelp" class="form-text text-muted">
                            @lang('Write your ID number according to your identification document.')
                        </small>
                    </div>

                    {{-- Phone number --}}
                    <div>
                        <label for="inputCustomerPhone" class="form-label">@lang('Mobile phone number')</label>
                        <div class="input-group">
                            @php
                                $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                            @endphp
                            <select class="form-select" style="max-width: 11em;"
                                wire:model.defer="customer_phone_country">
                                @foreach(collect(Countries::getList(app()->getLocale())) as $key => $val)
                                <option value="{{ $key }}">
                                    {{ $val }}
                                    @isset($phoneContryCodes[$key])({{ $phoneContryCodes[$key] }})@endisset
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

                    <x-slot name="footer">
                        <div>
                            <x-submit-button>@lang('Save')</x-submit-button>
                        </div>
                    </x-slot>
                </x-card>

            </div>
            <div class="col-sm-4">

                {{-- Credits --}}
                <x-card :title="__('Credit')">
                    <span class="display-6">@lang(':amount points', ['amount' => $customer->credit])</span>
                </x-card>

                {{-- Logout --}}
                <a
                    href="{{ route('customer.logout') }}"
                    class="btn btn-outline-danger d-block">
                    @lang('Logout')
                </a>
            </div>
        </div>
    </form>
</div>
