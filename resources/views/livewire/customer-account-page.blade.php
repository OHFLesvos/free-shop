@if($shouldDelete)
    <div class="small-container">
        <x-card :title="__('Delete account')">
            <p class="card-text">@lang('Do you really want do delete your customer account?')</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            wire:click="$toggle('shouldDelete')">
                            @lang('Cancel')
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            @lang('Delete')
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>
@else
    <div class="medium-container">
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

            <div class="row">
                <div class="col-sm-8">

                    <x-card :title="__('Customer Profile')">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="inputCustomerName" class="form-label">@lang('First & last name')</label>
                            <input type="text"
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

                        {{-- ID number --}}
                        <div class="mb-3">
                            <label for="inputCustomerIdNumber" class="form-label">@lang('ID number')</label>
                            <input type="text"
                                class="form-control @error('customer_id_number') is-invalid @enderror"
                                id="inputCustomerIdNumber"
                                wire:model.defer="customer_id_number"
                                required
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

                        {{-- Phone number --}}
                        <div>
                            <label
                                for="inputCustomerPhone"
                                class="form-label">@lang('Mobile phone number')</label>
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

                        <x-slot name="footer">
                            <div class="d-flex justify-content-between">
                                <x-submit-button>@lang('Save')</x-submit-button>
                                @if($this->canDelete)
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger"
                                        wire:loading.attr="disabled"
                                        wire:click="$toggle('shouldDelete')">
                                        @lang('Delete account')
                                    </button>
                                @endif
                            </div>
                        </x-slot>
                    </x-card>

                </div>
                <div class="col-sm-4">

                    {{-- Credits --}}
                    <x-card :title="__('Credit')">
                        <span class="display-6">@lang(':amount points', ['amount' => $customer->credit])</span>
                        @isset($customer->nextTopupDate)
                            <span class="card-text d-block mt-2">
                                @lang('Next top up on <strong>:date</strong>.', ['date' => $customer->nextTopupDate->isoFormat('LL')])
                            </span>
                        @endif
                    </x-card>

                </div>
            </div>
        </form>
    </div>
@endif
