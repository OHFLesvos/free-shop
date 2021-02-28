<div class="small-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card :title="__('Customer Login')">

            {{-- ID number --}}
            <div class="mb-3">
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
                    @if($request_name) disabled @endif
                    aria-describedby="customerIdNumberHelp">
                @error('customer_id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small id="customerIdNumberHelp" class="form-text text-muted">
                    @lang('Write your ID number according to your identification document.')
                    @if(setting()->has('customer.id_number_example'))
                        <br>@lang('Example: :value', ['value' => setting()->get('customer.id_number_example')])
                    @endif
                </small>
            </div>

            {{-- Phone number --}}
            <div>
                <label for="inputCustomerPhone" class="form-label">@lang('Mobile phone number')</label>
                <div class="input-group" dir="ltr">
                    @php
                        $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                    @endphp
                    <select
                        class="form-select"
                        style="max-width: 11em;"
                        @if($request_name) disabled @endif
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
                        @if($request_name) disabled @endif
                        aria-describedby="customerPhoneHelp">
                    @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <small id="customerPhoneHelp" class="form-text text-muted">
                    @lang('We will send updates about your order to this number.')
                </small>
            </div>

            {{-- Name --}}
            @if($request_name)
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
            @endif

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <x-spinner wire:loading wire:target="submit"/>
                        @lang('Login')
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>

@push('scripts')
<script>
    Livewire.on('nameRequired', () => {
        document.getElementById('inputCustomerName').focus();
    })
</script>
@endpush
