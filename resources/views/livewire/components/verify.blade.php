<div>
    <x-card :title="__('Verification')">
        @if($requestCode)
            <form wire:submit.prevent="verify" class="mb-4" autocomplete="off">
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
                        @if($verified) disabled @endif
                        dir="ltr"
                        aria-describedby="verificationCodeHelp">
                    @error('verificationCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small id="verificationCodeHelp" class="form-text text-muted">
                        {{ __('Please enter the verification code which you have received by SMS.') }}
                    </small>
                </div>
                <x-slot name="footer">
                    @if($verified)
                        <div class="text-center">
                            <x-spinner/>
                        </div>
                    @else
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
                                class="btn btn-primary"
                                wire:target="verify"
                                wire:loading.attr="disabled"
                            >
                                <x-spinner wire:loading wire:target="verify"/>
                                {{ __('Verify') }}
                            </button>
                        </div>
                    @endif
                </x-slot>
            </form>
        @else
            <p>{{ __('We will send you a code to verify your account.') }}</p>
            <button
                type="button"
                class="btn btn-primary"
                wire:click="sendCode('sms')"
                wire:target="sendCode"
                wire:loading.attr="disabled"
            >
                <x-spinner wire:loading wire:target="sendCode"/>
                {{ __('Send SMS to :phone', ['phone' => $this->phoneNumberFormatted]) }}
            </button>
            <button
                type="button"
                class="btn btn-link"
                wire:click="cancelVerify"
            >
                {{ __('Cancel') }}
            </button>
        @endif
    </x-card>
</div>
