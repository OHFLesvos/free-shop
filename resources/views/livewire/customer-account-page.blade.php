<div x-data="{ shouldDelete: false }">
    <div class="small-container" x-show="shouldDelete" x-cloak>
        <x-card :title="__('Delete account')">
            <p class="card-text">{{ __('Do you really want to delete your customer account?') }}</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            @click="shouldDelete = false">
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            {{ __('Delete') }}
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>

    <div class="medium-container" x-show="!shouldDelete">
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

            <div class="row">
                <div class="col-sm-8">

                    <x-card :title="__('Customer Profile')">
                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="nameInput" class="form-label">{{ __('First & last name') }}</label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="nameInput"
                                wire:model.defer="name"
                                required
                                autocomplete="off"
                                aria-describedby="nameHelp">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small id="nameHelp" class="form-text text-muted">
                                {{ __('Write your full name according to your identification document.') }}
                            </small>
                        </div>

                        {{-- ID number --}}
                        <div class="mb-3">
                            <label for="idNumberInput" class="form-label">{{ __('ID number') }}</label>
                            <input type="text"
                                class="form-control @error('idNumber') is-invalid @enderror"
                                id="idNumberInput"
                                wire:model.defer="idNumber"
                                required
                                autocomplete="off"
                                dir="ltr"
                                aria-describedby="idNumberHelp">
                            @error('idNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small id="idNumberHelp" class="form-text text-muted">
                                {{ __('Write your ID number according to your identification document.') }}
                                @if(setting()->has('customer.id_number_example'))
                                    <br>{{ __('Example: :value', ['value' => setting()->get('customer.id_number_example') ]) }}
                                @endif
                            </small>
                        </div>

                        {{-- Phone number --}}
                        <div class="mb-3">
                            <label
                                for="phoneInput"
                                class="form-label">{{ __('Mobile phone number') }}</label>
                            <div class="input-group" dir="ltr">
                                @php
                                    $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                                @endphp
                                <select
                                    class="form-select"
                                    style="max-width: 11em;"
                                    wire:model.defer="phoneCountry">
                                    @foreach(collect(Countries::getList(app()->getLocale())) as $key => $val)
                                        @isset($phoneContryCodes[$key])
                                            <option value="{{ $key }}">
                                                {{ __(':country (:code)', ['country' => $val, 'code' => $phoneContryCodes[$key]]) }}
                                            </option>
                                        @endisset
                                    @endforeach
                                </select>
                                <input
                                    type="tel"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="phoneInput"
                                    wire:model.defer="phone"
                                    autocomplete="off"
                                    aria-describedby="phoneHelp">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small id="phoneHelp" class="form-text text-muted">
                                {{ __('We will send updates about your order to this number.') }}
                            </small>
                        </div>

                        {{-- Email address --}}
                        <div>
                            <label for="emailInput" class="form-label">{{ __('Email address') }}</label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="emailInput"
                                autocomplete="off"
                                wire:model.defer="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small id="phoneHelp" class="form-text text-muted">
                                {{ __('We will send updates about your order to this email address.') }}
                            </small>
                        </div>

                        <x-slot name="footer">
                            <div class="d-flex justify-content-between">
                                <x-submit-button>{{ __('Save') }}</x-submit-button>
                                @if($this->canDelete)
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger"
                                        wire:loading.attr="disabled"
                                        @click="shouldDelete = true">
                                        {{ __('Delete account') }}
                                    </button>
                                @endif
                            </div>
                        </x-slot>
                    </x-card>

                </div>
                <div class="col-sm-4">

                    {{-- Balance --}}
                    <x-card :title="__('Balance')">
                        <p class="card-text">
                            @forelse ($customer->balance() as $k => $v)
                                <strong>{{ $v }}</strong> {{ $k }} <br>
                            @empty
                                <strong class="text-warning">{{ __("You currently don't have any points available to spend.") }}</strong>
                            @endforelse
                        </p>
                        @if(setting()->has('customer.credit_top_up.days'))
                            @isset($customer->topped_up_at)
                                <p class="card-text">
                                    {!! __('Last topped up on <strong>:date</strong>.', ['date' => $customer->topped_up_at->isoFormat('LL') ]) !!}
                                </p>
                            @endif
                            @isset($customer->nextTopUpDate)
                                <p class="card-text">
                                    {!! __('Next top-up on <strong>:date</strong>.', ['date' => $customer->nextTopUpDate->isoFormat('LL') ]) !!}
                                </p>
                            @endif
                        @endif
                    </x-card>

                </div>
            </div>
        </form>
    </div>
</div>
