<div>
    @if($shouldDelete)
        <h1 class="mb-3">Delete product</h1>
        <p>Really delete the product <strong>{{ $product->name }}</strong>?</p>
        <p class="d-flex justify-content-between">
            <button
                type="button"
                class="btn btn-outline-primary"
                wire:loading.attr="disabled"
                wire:click="$toggle('shouldDelete')">
                Cancel
            </button>
            <button
                type="button"
                class="btn btn-outline-danger"
                wire:target="delete"
                wire:loading.attr="disabled"
                wire:click="delete">
                <x-spinner wire:loading wire:target="delete"/>
                Delete
            </button>
        </p>
    @else
        <div class="d-md-flex justify-content-between align-items-center">
            <h1 class="mb-3">
                @if($product->exists)
                    Edit Product
                @else
                    Register Product
                @endif
            </h1>
            @if(count(config('app.supported_languages')) < 3)
                <div class="btn-group mb-3">
                    @foreach(config('app.supported_languages') as $lang_key => $lang_name)
                        <button
                            class="btn @if($lang_key == $locale) btn-primary @else btn-outline-primary @endif"
                            wire:click="$set('locale', '{{ $lang_key }}')"
                            wire:loading.attr="disabled"
                        >
                            {{ $lang_name }} ({{ strtoupper($lang_key) }})
                        </button>
                    @endforeach
                </div>
            @else
                <div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Language</span>
                        </div>
                        <select class="custom-select" wire:model.lazy="locale">
                            @foreach(config('app.supported_languages') as $lang_key => $lang_name)
                                <option
                                    value="{{ $lang_key }}">
                                    {{ $lang_name }} ({{ strtoupper($lang_key) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
        <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
            <div class="form-row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ strtoupper($locale) }}</span>
                            </div>
                            <input
                                type="text"
                                class="form-control @error('name.' . $locale) is-invalid @enderror"
                                id="inputName"
                                @if($this->defaultLocale == $locale) required @endif
                                @if($this->defaultLocale != $locale) placeholder="{{ $name[$this->defaultLocale] ?? '' }}" @endif
                                autocomplete="off"
                                @unless($product->exists) autofocus @endunless
                                wire:model.lazy="name.{{ $locale }}">
                            @error('name.' . $locale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputCategory">Category</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ strtoupper($locale) }}</span>
                            </div>
                            <input
                                type="text"
                                class="form-control @error('category.' . $locale) is-invalid @enderror"
                                id="inputCategory"
                                @if($this->defaultLocale == $locale) required @endif
                                @if($this->defaultLocale != $locale) placeholder="{{ $category[$this->defaultLocale] ?? '' }}" @endif
                                autocomplete="off"
                                list="categories"
                                wire:model.lazy="category.{{ $locale }}">
                            @error('category.' . $locale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <datalist id="categories">
                            @foreach($categories[$locale] as $category)
                                <option value="{{ $category }}"/>
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputStock">Stock</label>
                        <input
                            type="number"
                            min="0"
                            class="form-control @error('product.stock') is-invalid @enderror"
                            id="inputStock"
                            required
                            autocomplete="off"
                            wire:model.defer="product.stock">
                        @error('product.stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputLimitPerOrder">Limit per order</label>
                        <input
                            type="number"
                            min="0"
                            class="form-control @error('product.limit_per_order') is-invalid @enderror"
                            id="inputLimitPerOrder"
                            autocomplete="off"
                            wire:model.defer="product.limit_per_order">
                        @error('product.limit_per_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="custom-control custom-checkbox mb-3">
                <input
                    type="checkbox"
                    class="custom-control-input"
                    id="isAvailableInput"
                    value="1"
                    wire:model.defer="product.is_available">
                <label class="custom-control-label" for="isAvailableInput">Available</label>
            </div>
            <div class="form-row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="inputDescription">Description</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ strtoupper($locale) }}</span>
                            </div>
                            <textarea
                                type="text"
                                class="form-control @error('description') is-invalid @enderror"
                                id="inputDescription"
                                rows="3"
                                autocomplete="off"
                                @if($this->defaultLocale != $locale) placeholder="{{ $description[$this->defaultLocale] ?? '' }}" @endif
                                wire:model.lazy="description.{{ $locale }}"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <label for="pictureInput">Picture</label>
                    <div class="custom-file mb-3">
                        <input
                            type="file"
                            class="custom-file-input"
                            wire:model="picture"
                            accept="image/*"
                            id="pictureInput">
                        <label class="custom-file-label" for="pictureInput">Choose file</label>
                    </div>
                    @error('picture') <span class="text-danger">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="picture">Uploading...</div>
                    <div wire:loading.remove wire:target="picture">
                    @if($picture)
                        <div class="mb-3">
                            @php
                                if (config('filesystems.default') == 'ftp') {
                                    $file = basename(parse_url($picture->temporaryUrl())['path']);
                                    $previewUrl = config('filesystems.disks.ftp.url') . "/livewire-tmp/" . $file;
                                } else {
                                    $previewUrl = $picture->temporaryUrl();
                                }
                            @endphp
                            <img
                                src="{{ $previewUrl }}"
                                alt="Preview"
                                class="mb-3"
                                style="max-width: 300px; max-height: 150px">
                            <br>
                            <button
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                wire:click="$set('picture', null)">
                                Undo
                            </button>
                        </div>
                    @elseif(isset($product->picture))
                        @unless($removePicture)
                            <div class="mb-3">
                                <img
                                    src="{{ $product->pictureUrl }}"
                                    alt="Preview"
                                    class="mb-2"
                                    style="max-width: 300px; max-height: 150px">
                                <br>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    wire:click="$toggle('removePicture')">
                                    Remove picture
                                </button>
                            </div>
                        @else
                            <p>Picture will be removed.
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    wire:click="$toggle('removePicture')">
                                    Undo
                                </button>
                            </p>
                        @endunless
                    @endif
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <a
                    href="{{ route('backend.products') }}"
                    class="btn btn-outline-primary">Back to products</a>
                <span>
                    @if($product->exists && $product->orders->isEmpty())
                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            wire:loading.attr="disabled"
                            wire:click="$toggle('shouldDelete')">
                            Delete
                        </button>
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
        </form>
    @endif
</div>
