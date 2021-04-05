<div class="small-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card :title="$this->heading">

            {{-- ID number --}}
            @if($state == 'enter_id_number')
                <div>
                    <label for="inputCustomerIdNumber" class="form-label">@lang('ID number')</label>
                    <input
                        type="text"
                        class="form-control @error('customer_id_number') is-invalid @enderror"
                        id="inputCustomerIdNumber"
                        wire:model.defer="customer_id_number"
                        required
                        autofocus
                        autocomplete="off"
                        dir="ltr"
                        aria-describedby="customerIdNumberHelp">
                    @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small id="customerIdNumberHelp" class="form-text text-muted">
                        @lang('Write your ID number according to your identification document.')
                        @if(setting()->has('customer.id_number_example'))
                            <br>@lang('Example: :value', ['value' => setting()->get('customer.id_number_example')])
                        @endif
                    </small>
                </div>
            @elseif(in_array($state, ['enter_name', 'enter_phone', 'ask_for_tfa', 'validate_phone']))
                <p>
                    @lang('ID number'):
                    <strong>{{ $customer_id_number }}</strong>
                    <span class="ms-2">
                        [<a href="#" wire:click="changeState('enter_id_number')">@lang('Change')</a>]
                    </span>
                </p>
            @endif

            {{-- Name --}}
            @if($state == 'enter_name')
                <div class="mt-3">
                    <label for="inputCustomerName" class="form-label">@lang('First & last name')</label>
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
            @elseif(in_array($state, ['enter_phone', 'validate_phone']))
                <p>
                    @lang('Name'):
                    <strong>{{ $customer_name }}</strong>
                    <span class="ms-2">
                        [<a href="#" wire:click="changeState('enter_name')">@lang('Change')</a>]
                    </span>
                </p>
            @endif

            {{-- Phone number --}}
            @if($state == 'enter_phone')
                <div>
                    <label for="inputCustomerPhone" class="form-label">@lang('Mobile phone number')</label>
                    <div class="input-group" dir="ltr">
                        @php
                            $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                        @endphp
                        <select
                            class="form-select"
                            style="max-width: 11em;"
                            wire:model.defer="customer_phone_country">
                            @foreach(collect(Countries::getList(app()->getLocale())) as $key => $val)
                                @isset($phoneContryCodes[$key])
                                    <option value="{{ $key }}">
                                        @lang(':country (:code)', ['country' => $val, 'code' => $phoneContryCodes[$key]])
                                    </option>
                                @endisset
                            @endforeach
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
            @endif

            {{-- OTP verification --}}
            @if(in_array($state, ['ask_for_tfa', 'validate_phone']))
                <p>
                    @if(session()->has('otpDelay'))
                        <x-alert type="warning">{{ session()->get('otpDelay') }}</x-alert>
                    @endif
                    @if(session()->has('error'))
                        <x-alert type="danger">{{ session()->get('error') }}</x-alert>
                    @endif                    
                    @if($state == 'ask_for_tfa')
                        @if(session()->has('otpDelay'))
                            @lang("Please check the code we've sent to <strong>:phone</strong>", ['phone' => maskString($customer->phone, 6, 2)])
                        @else
                            @lang("We've sent a login code to your phone <strong>:phone</strong>", ['phone' => maskString($customer->phone, 6, 2)])
                        @endif
                    @elseif($state == 'validate_phone')
                        @if(session()->has('otpDelay'))
                            @lang("Please check the code we've sent to <strong>:phone</strong>", ['phone' => $this->customerPhoneE164])
                        @else
                            @lang("We've sent a validation code to <strong>:phone</strong>", ['phone' => $this->customerPhoneE164])
                        @endif
                        <span class="ms-2">
                            [<a href="#" wire:click="changeState('enter_phone')">@lang('Change')</a>]
                        </span>
                    @endif
                </p>
                <div>
                    <label for="inputOtpValue" class="form-label">@lang('Please enter the code:')</label>
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control @error('otp_value') is-invalid @enderror"
                            id="inputOtpValue"
                            wire:model.defer="otp_value"
                            required
                            maxlength="{{ $otp_length }}"
                            autofocus
                            autocomplete="off"
                            dir="ltr">
                        @error('otp_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    @if($state != 'enter_id_number')
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:key="initial"
                            wire:click="initialState()">
                            @lang('Cancel')
                        </button>
                    @endisset
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <x-spinner wire:loading wire:target="submit"/>
                        @lang('Next')
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>

@push('scripts')
<script>
    Livewire.on('idNumberRequired', () => {
        document.getElementById('inputCustomerIdNumber').focus();
    })
    Livewire.on('nameRequired', () => {
        document.getElementById('inputCustomerName').focus();
    })
    Livewire.on('phoneRequired', () => {
        document.getElementById('inputCustomerPhone').focus();
    })
    Livewire.on('otpRequired', () => {
        document.getElementById('inputOtpValue').focus();
    })

</script>
@endpush
