@if($shouldDelete)
    <div class="small-container">
        <x-card title="Delete customer">
            <p class="card-text">Really delete the customer <strong>{{ $customer->name }}</strong>?</p>
            <x-slot name="footer">
                <div class="d-flex justify-content-end">
                    <span>
                        <button
                            type="button"
                            class="btn btn-link"
                            wire:loading.attr="disabled"
                            wire:click="$toggle('shouldDelete')">
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:target="delete"
                            wire:loading.attr="disabled"
                            wire:click="delete">
                            <x-spinner wire:loading wire:target="delete"/>
                            Delete
                        </button>
                    </span>
                </div>
            </x-slot>
        </x-card>
    </div>
@else
    <div class="medium-container">
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <x-card :title="$title">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inputName" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control @error('customer.name') is-invalid @enderror"
                            id="inputName"
                            required
                            @unless($customer->exists) autofocus @endunless
                            autocomplete="off"
                            wire:model.defer="customer.name">
                        @error('customer.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="inputIdNumber" class="form-label">ID Number</label>
                        <input
                            type="text"
                            class="form-control @error('customer.id_number') is-invalid @enderror"
                            id="inputIdNumber"
                            required
                            autocomplete="off"
                            wire:model.defer="customer.id_number">
                        @error('customer.id_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="inputPhone" class="form-label">Phone</label>
                        <div class="input-group">
                            @php
                                $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                            @endphp
                            <select
                                class="form-select"
                                style="max-width: 11em;"
                                wire:model.defer="customer_phone_country">
                                <option value="" selected>-- Select country --</option>
                                @foreach(Countries::getList() as $key => $val)
                                    <option value="{{ $key }}">
                                        {{ $val }}
                                        @isset($phoneContryCodes[$key] )({{ $phoneContryCodes[$key] }})@endisset
                                    </option>
                                @endforeach
                            </select>
                            <input
                                type="tel"
                                class="form-control @error('customer_phone') is-invalid @enderror"
                                id="inputPhone"
                                required
                                autocomplete="off"
                                wire:model.defer="customer_phone">
                            @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="inputCredit" class="form-label">Credit</label>
                        <input
                            type="number"
                            class="form-control @error('customer.credit') is-invalid @enderror"
                            id="inputCredit"
                            required
                            min="0"
                            autocomplete="off"
                            wire:model.defer="customer.credit">
                        @error('customer.credit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="inputLocale" class="form-label">Language</label>
                        <select
                            id="inputLocale"
                            class="form-select @error('customer.locale') is-invalid @enderror"
                            style="max-width: 11em;"
                            wire:model.defer="customer.locale">
                            <option value="" selected>-- Select language --</option>
                            @foreach(config('app.supported_languages') as $key => $val)
                                <option value="{{ $key }}">
                                    {{ $val }} ({{ strtoupper($key) }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer.locale') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    </div>
                    <div class="col-md-12">
                        <label for="inputRemarks" class="form-label">Remarks</label>
                        <textarea
                            class="form-control @error('customer.remarks') is-invalid @enderror"
                            id="inputRemarks"
                            autocomplete="off"
                            rows="3"
                            wire:model.defer="customer.remarks"></textarea>
                        @error('customer.remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="isDisabledInput"
                                value="1"
                                wire:model="customer.is_disabled">
                            <label class="form-check-label" for="isDisabledInput">Disabled</label>
                            @error('customer.is_disabled') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    @if($customer->is_disabled)
                        <div class="col-md-8">
                            <label for="disabledReasonInput" class="form-label">Reason for disabling</label>
                            <textarea
                                class="form-control @error('customer.disabled_reason') is-invalid @enderror"
                                id="disabledReasonInput"
                                autocomplete="off"
                                rows="3"
                                wire:model.defer="customer.disabled_reason"></textarea>
                            @error('customer.disabled_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif
                </div>

                <x-slot name="footer">
                    <div class="d-flex justify-content-between">
                        <span>
                            @if($customer->exists)
                                @can('delete', $customer)
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:loading.attr="disabled"
                                        wire:click="$toggle('shouldDelete')">
                                        Delete
                                    </button>
                                @endcan
                            @endif
                        </span>
                        <span>
                            @if($customer->exists)
                                @can('view', $customer)
                                    <a
                                        href="{{ route('backend.customers.show', $customer) }}"
                                        class="btn btn-link">Cancel</a>
                                @endcan
                            @else
                                @can('viewAny', App\Models\Customer::class)
                                    <a
                                        href="{{ route('backend.customers') }}"
                                        class="btn btn-link">Cancel</a>
                                @endcan
                            @endif
                            <button
                                type="submit"
                                class="btn btn-primary"
                                wire:target="submit"
                                wire:loading.attr="disabled">
                                <x-spinner wire:loading wire:target="submit"/>
                                Save
                            </button>
                        </span>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>
@endif
