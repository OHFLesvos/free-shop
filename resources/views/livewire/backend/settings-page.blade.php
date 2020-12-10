<div>
    <h1>Settings</h1>
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <div class="card mb-4">
            <div class="card-header">Geoblock Whitelist</div>
            <div class="card-body">
                <p class="card-text">Select countries from which clients would be able to access the shop. If left empty, all countries are allowed.</p>
                @php
                    $list = $this->countries->filter(fn ($val, $key) => $geoblockWhitelist->contains($key))
                @endphp
                @if($list->isNotEmpty())
                    <div class="mb-3">
                        @foreach($list as $key => $val)
                            <button
                                type="button"
                                class="btn btn-warning mr-2"
                                wire:click="removeFromGeoblockWhitelist('{{ $key }}')">
                                {{ $val }}
                            </button>
                        @endforeach
                    </div>
                @endif
                <div class="input-group" style="max-width: 20em;">
                    <select class="custom-select" wire:model.lazy="selectedCountry">
                        <option value="" selected>-- Select country --</option>
                        @foreach($countries->filter(fn ($val, $key) => ! $geoblockWhitelist->contains($key)) as $key => $val)
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
            </div>
        </div>
        <button
            type="submit"
            class="btn btn-primary"
            wire:target="submit">
            <x-bi-hourglass-split wire:loading wire:target="submit"/>
            Save
        </button>
     </form>
</div>
