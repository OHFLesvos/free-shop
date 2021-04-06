<div class="small-container">
    @if(session()->has('error'))
        <x-alert type="warning" dismissible>{{ session()->get('error') }}</x-alert>
    @endif
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card :title="__('Customer Login')">
            <div>
                <label for="customerIdNumberInput" class="form-label">@lang('ID number')</label>
                <input
                    type="text"
                    class="form-control @error('customerIdNumber') is-invalid @enderror"
                    id="customerIdNumberInput"
                    wire:model.defer="customerIdNumber"
                    required
                    autofocus
                    autocomplete="off"
                    dir="ltr"
                    aria-describedby="customerIdNumberHelp">
                @error('customerIdNumber') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small id="customerIdNumberHelp" class="form-text text-muted">
                    @lang('Write your ID number according to your identification document.')
                    @if(setting()->has('customer.id_number_example'))
                        <br>@lang('Example: :value', ['value' => setting()->get('customer.id_number_example')])
                    @endif
                </small>
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-end">
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
</script>
@endpush
