@php
$countries = collect(Countries::getList());
@endphp
<div>
    <h1 class="mb-3">Settings</h1>
    @if (session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">

        <div class="card shadow-sm mb-4">
            <div class="card-header">Geoblock Whitelist</div>
            <div class="card-body">
                <p class="card-text">Select countries from which clients are able to access the shop. If left empty, all
                    countries are allowed.</p>
                @php
                $list = $countries->filter(fn ($val, $key) => $geoblockWhitelist->contains($key))
                @endphp
                @if ($list->isNotEmpty())
                    <div class="mb-3">
                        @foreach ($list as $key => $val)
                            <button type="button" class="btn btn-warning mr-2"
                                wire:click="removeFromGeoblockWhitelist('{{ $key }}')">
                                {{ $val }}
                            </button>
                        @endforeach
                    </div>
                @endif
                <div class="input-group" style="max-width: 20em;">
                    <select class="custom-select" wire:model.lazy="selectedCountry">
                        <option value="" selected>-- Select country --</option>
                        @foreach ($countries->filter(fn($val, $key) => !$geoblockWhitelist->contains($key)) as $key => $val)
                            <option value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" wire:click="addToGeoblockWhitelist">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">Customer</div>
            <div class="card-body pb-1">
                <div class="row">
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="orderDefaultPhoneCountry" class="d-block">Default country for phone
                                number</label>
                            @php
                            $codes = megastruktur\PhoneCountryCodes::getCodesList();
                            @endphp
                            <select class="custom-select @error('orderDefaultPhoneCountry') is-invalid @enderror"
                                style="max-width: 20em;" wire:model.defer="orderDefaultPhoneCountry"
                                id="orderDefaultPhoneCountry">
                                <option value="">-- Select country --</option>
                                @foreach ($countries as $key => $val)
                                    <option value="{{ $key }}">
                                        {{ $val }}
                                        @isset($codes[$key])({{ $codes[$key] }})@endisset
                                    </option>
                                @endforeach
                            </select>
                            @error('orderDefaultPhoneCountry') <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="timezone" class="d-block">Default timezone:</label>
                            <select id="timezone" wire:model.defer="timezone"
                                class="custom-select @error('timezone') is-invalid @enderror" style="max-width: 20em;">
                                <option value="">- Default timezone -</option>
                                @foreach (listTimezones() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="customerStartingCredit" class="d-block">Starting credit:</label>
                            <input type="number" min="0" id="customerStartingCredit"
                                wire:model.defer="customerStartingCredit"
                                class="custom-select @error('customerStartingCredit') is-invalid @enderror">
                            @error('customerStartingCredit') <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">Content</div>
            <div class="card-body pb-1">
                <div class="form-group">
                    <label for="welcomeText" class="d-block">Welcome page text:</label>
                    <textarea id="welcomeText" wire:model.defer="welcomeText" rows="5"
                        class="form-control @error('welcomeText') is-invalid @enderror"
                        aria-describedby="welcomeTextHelp">
                    </textarea>
                    @error('welcomeText') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small id="welcomeTextHelp" class="form-text text-muted">
                        You can use <a href="https://commonmark.org/help/" target="_blank">Markdown syntax</a> to format
                        the text.
                    </small>
                </div>
            </div>
        </div>

        <p class="text-right">
            <button type="submit" class="btn btn-primary">
                <x-spinner wire:loading wire:target="submit" />
                Save
            </button>
        </p>
    </form>
</div>
