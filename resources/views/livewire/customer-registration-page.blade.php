<div class="small-container">
    @if(session()->has('error'))
        <x-alert type="warning" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card :title="__('Customer Registration')">

            <div>
                <label for="idNumberInput" class="form-label">{{ __('ID number') }}</label>
                <input
                    type="text"
                    class="form-control @error('idNumber') is-invalid @enderror"
                    id="idNumberInput"
                    wire:model.defer="idNumber"
                    required
                    @if(blank($idNumber)) autofocus @endif
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

            <div class="mt-3">
                <label for="nameInput" class="form-label">{{ __('First & last name') }}</label>
                <input
                    type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="nameInput"
                    wire:model.defer="name"
                    required
                    @unless(blank($idNumber)) autofocus @endunless
                    autocomplete="off"
                    aria-describedby="nameHelp">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small id="nameHelp" class="form-text text-muted">
                    {{ __('Write your full name according to your identification document.') }}
                </small>
            </div>

            <div>
                <label for="phoneInput" class="form-label">{{ __('Mobile phone number') }}</label>
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
                        required
                        autocomplete="off"
                        aria-describedby="phoneHelp">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <small id="phoneHelp" class="form-text text-muted">
                    {{ __('We will send updates about your order to this number.') }}
                </small>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    <a
                        class="btn btn-link"
                        href="{{ route('customer.login') }}">
                        {{ __('Cancel') }}
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary">
                        <x-spinner wire:loading wire:target="submit"/>
                        {{ __('Next') }}
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>

@push('scripts')
<script>
</script>
@endpush
