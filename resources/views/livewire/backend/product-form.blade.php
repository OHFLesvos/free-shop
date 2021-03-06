@if($shouldDelete)
    <div class="small-container">
        <x-card title="Delete product">
            <p class="card-text">Really delete the product <strong>{{ $product->name }}</strong>?</p>
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
        <form wire:submit.prevent="submit" autocomplete="off">
            <x-card :title="$title">
                <x-slot name="header">
                    <div class="d-flex justify-content-end align-items-center">
                        @if(count(config('app.supported_languages')) < 3)
                            <div class="btn-group">
                                @foreach(config('app.supported_languages') as $lang_key => $lang_name)
                                    <button
                                        type="button"
                                        class="btn @if($lang_key == $locale) btn-primary @else btn-outline-primary @endif"
                                        wire:click="$set('locale', '{{ $lang_key }}')"
                                        wire:loading.attr="disabled"
                                    >
                                        {{ $lang_name }} ({{ strtoupper($lang_key) }})
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <span class="me-2">Language:</span>
                            <select class="form-select w-auto" wire:model.lazy="locale">
                                @foreach(config('app.supported_languages') as $lang_key => $lang_name)
                                    <option
                                        value="{{ $lang_key }}">
                                        {{ $lang_name }} ({{ strtoupper($lang_key) }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </x-slot>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="inputName" class="form-label">Name</label>
                        <div class="input-group" @if(in_array($locale, config('app.rtl_languages', []))) dir="rtl" @endif>
                            <span class="input-group-text">{{ strtoupper($locale) }}</span>
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
                    <div class="col-md-6">
                        <label for="inputCategory" class="form-label">Category</label>
                        <div class="input-group" @if(in_array($locale, config('app.rtl_languages', []))) dir="rtl" @endif>
                            <span class="input-group-text">{{ strtoupper($locale) }}</span>
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
                    <div class="col-md-3">
                        <label for="inputSequence" class="form-label">Order sequence</label>
                        <input
                            type="number"
                            min="0"
                            class="form-control @error('product.sequence') is-invalid @enderror"
                            id="inputSequence"
                            required
                            autocomplete="off"
                            wire:model.defer="product.sequence">
                        @error('product.sequence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="inputPrice" class="form-label">Price</label>
                        <input
                            type="number"
                            min="0"
                            class="form-control @error('product.price') is-invalid @enderror"
                            id="inputPrice"
                            required
                            autocomplete="off"
                            wire:model.defer="product.price">
                        @error('product.price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="inputLimitPerOrder" class="form-label">Limit per order</label>
                        <input
                            type="number"
                            min="0"
                            class="form-control @error('product.limit_per_order') is-invalid @enderror"
                            id="inputLimitPerOrder"
                            autocomplete="off"
                            wire:model.defer="product.limit_per_order">
                        @error('product.limit_per_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="isAvailableInput"
                                value="1"
                                wire:model.defer="product.is_available">
                            <label class="form-check-label" for="isAvailableInput">Available</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="inputDescription" class="form-label">Description</label>
                        <div class="input-group" @if(in_array($locale, config('app.rtl_languages', []))) dir="rtl" @endif>
                            <span class="input-group-text">{{ strtoupper($locale) }}</span>
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
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="pictureInput" class="form-label">Picture</label>
                            <input
                                type="file"
                                class="form-control"
                                wire:model="picture"
                                accept="image/*"
                                id="pictureInput">
                            @error('picture') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div wire:loading wire:target="picture"><x-spinner/> Uploading...</div>
                        <div wire:loading.remove wire:target="picture">
                            @if($picture)
                                <div>
                                    <img
                                        src="{{ $picture->temporaryUrl() }}"
                                        alt="Preview"
                                        class="mb-3 img-thumbnail img-fluid">
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
                                    <div>
                                        <img
                                            src="{{ url($product->pictureUrl) }}"
                                            alt="Preview"
                                            class="mb-3 img-thumbnail img-fluid">
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

                <x-slot name="footer">
                    <div class="d-flex justify-content-between">
                        <span>
                            @if($product->exists)
                                @can('delete', $product)
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:loading.attr="disabled"
                                        wire:click="$toggle('shouldDelete')">
                                        Delete
                                    </button>
                                @endif
                            @endif
                        </span>
                        <span>
                            @can('viewAny', App\Models\Product::class)
                                <a
                                    href="{{ route('backend.products') }}"
                                    class="btn btn-link">Cancel</a>
                            @endcan
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
