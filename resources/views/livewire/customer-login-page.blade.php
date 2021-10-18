<div class="small-container">
    @if(session()->has('error'))
        <x-alert type="warning" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    @if($showVerify)
        <form wire:submit.prevent="verify" class="mb-4" autocomplete="off">
            <x-card :title="__('Verification')">
                <div>
                    <label for="verificationCodeInput" class="form-label">{{ __('Verification code') }}</label>
                    <input
                        type="text"
                        class="form-control @error('verificationCode') is-invalid @enderror"
                        id="verificationCodeInput"
                        wire:model.defer="verificationCode"
                        required
                        autofocus
                        autocomplete="off"
                        dir="ltr"
                        aria-describedby="verificationCodeHelp">
                    @error('verificationCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small id="verificationCodeHelp" class="form-text text-muted">
                        {{ __('Please enter the verification code which you have received by SMS.') }}
                    </small>
                </div>
                <x-slot name="footer">
                    <div class="d-flex justify-content-between">
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:click="cancelVerify"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary">
                            <x-spinner wire:loading wire:target="verify"/>
                            {{ __('Verify') }}
                        </button>
                    </div>
                </x-slot>
            </x-card>
        </form>
    @else
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <x-card :title="__('Customer Registration & Login')">
                <div>
                    <label for="idNumberInput" class="form-label">{{ __('ID number') }}</label>
                    <input
                        type="text"
                        class="form-control @error('idNumber') is-invalid @enderror"
                        id="idNumberInput"
                        wire:model.defer="idNumber"
                        required
                        autofocus
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
                <p class="card-text mt-3 d-flex align-items-center">
                    <x-icon icon="info-circle" class="fa-2x me-3"/>
                    <span>{{ __("If you don't have an account yet, we will ask you to register a new account in the next step.") }}</span>
                </p>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end">
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
    @endif
</div>
