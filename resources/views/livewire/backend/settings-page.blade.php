<div class="medium-container">

    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif

    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

        <x-card title="General settings">
            <div class="mb-3">
                <label for="timezone" class="form-label">Default timezone:</label>
                <select
                    id="timezone"
                    wire:model.defer="timezone"
                    class="form-select @error('timezone') is-invalid @enderror"
                    style="max-width: 20em;">
                    <option value="">- Default timezone ({{ config('app.timezone') }}) -</option>
                    @foreach (listTimezones() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="brandLogoInput" class="form-label">Brand Logo:</label>
                <input
                    type="file"
                    class="form-control"
                    wire:model="brandLogoUpload"
                    accept="image/*"
                    id="brandLogoInput">
                @error('brandLogoUpload') <span class="text-danger">{{ $message }}</span> @enderror
                <div wire:loading wire:target="brandLogoUpload" class="mt-3">
                    <x-spinner/> Uploading...
                </div>
                @if(isset($brandLogoUpload))
                    <div class="mt-3">
                        <img
                            src="{{ $brandLogoUpload->temporaryUrl() }}"
                            alt="Preview"
                            height="24"/>
                    </div>
                @elseif(isset($brandLogo))
                    <div class="d-flex align-items-center mt-3">
                    <div class="me-2">
                        <img
                            src="{{ url(Storage::url($brandLogo)) }}"
                            alt="Current logo"
                            height="24"/>
                    </div>
                    <div class="form-check form-switch">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="brandLogoRemoveInput"
                            value="1"
                            wire:model.defer="brandLogoRemove">
                        <label class="form-check-label" for="brandLogoRemoveInput">Remove existing logo</label>
                    </div>
                </div>
                @endif
            </div>
        </x-card>

        <x-card title="Shop">
            <div class="row">
                <div class="col-sm">
                    <div class="form-check form-switch mb-3">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="shopDisabledInput"
                            value="1"
                            wire:model.defer="shopDisabled">
                        <label class="form-check-label" for="shopDisabledInput">Disable shop</label>
                    </div>
                </div>
                <div class="col-sm">
                    <div>
                        <label for="shopMaxOrdersPerDayInput" class="form-label">Maximum orders per day:</label>
                        <input
                            type="number"
                            min="1"
                            id="shopMaxOrdersPerDayInput"
                            wire:model.defer="shopMaxOrdersPerDay"
                            class="form-control @error('shopMaxOrdersPerDay') is-invalid @enderror"
                            style="max-width: 10em;"
                            aria-describedby="shopMaxOrdersPerDayHelp">
                        @error('shopMaxOrdersPerDay') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="shopMaxOrdersPerDayHelp" class="form-text">
                            Leave empty to disable the limit.
                        </small>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Geoblocking">
            <p class="card-text mb-0">
                Select countries from which clients are able to access the shop.
                If left empty, all countries are allowed.
            </p>
            <p class="card-text">
                <small><strong>Note:</strong> Your current country is <em>{{ geoip()->getLocation()['country'] }}</em>.</small>
            </p>
            @php
                $list = $countries->filter(fn ($val, $key) => $geoblockWhitelist->contains($key))
            @endphp
            @if ($list->isNotEmpty())
                <div class="mb-3">
                    @foreach ($list as $key => $val)
                        <button type="button" class="btn btn-warning me-2"
                            wire:click="removeFromGeoblockWhitelist('{{ $key }}')">
                            {{ $val }}
                        </button>
                    @endforeach
                </div>
            @endif
            <div class="input-group" style="max-width: 20em;">
                <select
                    class="form-select"
                    wire:model.lazy="selectedCountry">
                    <option value="" selected>-- Select country --</option>
                    @foreach ($countries->filter(fn($val, $key) => !$geoblockWhitelist->contains($key)) as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        wire:click="addToGeoblockWhitelist">
                        Add
                    </button>
                </div>
            </div>
        </x-card>

        <x-card title="Customer" no-footer-padding>
            <div class="row">
                <div class="col-sm">
                    <div class="mb-3">
                        <label for="orderDefaultPhoneCountry" class="form-label">
                            Default country for phone number:
                        </label>
                        @php
                            $phoneContryCodes = megastruktur\PhoneCountryCodes::getCodesList();
                        @endphp
                        <select
                            class="form-select @error('orderDefaultPhoneCountry') is-invalid @enderror"
                            style="max-width: 20em;"
                            wire:model.defer="orderDefaultPhoneCountry"
                            id="orderDefaultPhoneCountry">
                            <option value="">-- Select country --</option>
                            @foreach ($countries as $key => $val)
                                <option value="{{ $key }}">
                                    {{ $val }}
                                    @isset($phoneContryCodes[$key])({{ $phoneContryCodes[$key] }})@endisset
                                </option>
                            @endforeach
                        </select>
                        @error('orderDefaultPhoneCountry') <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="mb-3">
                        <label for="customerStartingCredit" class="form-label">Starting credit:</label>
                        <input
                            type="number"
                            min="0"
                            id="customerStartingCredit"
                            wire:model.defer="customerStartingCredit"
                            class="form-control @error('customerStartingCredit') is-invalid @enderror"
                            style="max-width: 10em;">
                        @error('customerStartingCredit') <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                </div>
        </x-card>

        <x-card title="Content">
            <div>
                <label for="welcomeText" class="form-label">Welcome page text:</label>
                <ul class="nav nav-tabs mb-2">
                    <li class="nav-item">
                        <a
                            class="nav-link @unless($welcomeTextPreview) active @endunless"
                            @unless($welcomeTextPreview) aria-current="page" @endunless
                            href="#editor"
                            wire:click="$set('welcomeTextPreview', false)">
                            Editor
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link @if($welcomeTextPreview) active @endif"
                            @if($welcomeTextPreview) aria-current="page" @endif
                            href="#preview"
                            wire:click="$set('welcomeTextPreview', true)">
                            Preview
                        </a>
                    </li>
                </ul>
                @unless($welcomeTextPreview)
                    <textarea
                        id="welcomeText"
                        wire:model.lazy="welcomeText"
                        rows="5"
                        class="form-control @error('welcomeText') is-invalid @enderror"
                        aria-describedby="welcomeTextHelp">
                    </textarea>
                    @error('welcomeText') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small id="welcomeTextHelp" class="form-text">
                        You can use <a href="https://commonmark.org/help/" target="_blank">Markdown syntax</a> to format the text.
                    </small>
                @else
                    @if($welcomeText != null)
                        {!! Str::of($welcomeText)->markdown() !!}
                    @else
                        <x-alert type="info mb-0">
                            No preview available.
                        </x-alert>
                    @endif
                @endunless
            </div>
        </x-card>

        <p>
            <x-submit-button>Save</x-submit-button>
        </p>
    </form>
</div>
