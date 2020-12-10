<div>
    <h1 class="mb-3">
        @if($product->exists)
            Edit Product
        @else
            Register Product
        @endif
    </h1>
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <div class="form-row">
            <div class="col-md">
                <div class="form-group">
                    <label for="inputName">Name</label>
                    <input
                        type="text"
                        class="form-control @error('product.name') is-invalid @enderror"
                        id="inputName"
                        required
                        autocomplete="off"
                        @unless($product->exists) autofocus @endunless
                        wire:model.defer="product.name">
                    @error('product.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label for="inputCategory">Category</label>
                    <input
                        type="text"
                        class="form-control @error('product.category') is-invalid @enderror"
                        id="inputCategory"
                        required
                        autocomplete="off"
                        wire:model.defer="product.category">
                    @error('product.category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col-md">
                <div class="form-group">
                    <label for="inputDescription">Description</label>
                    <textarea
                        type="text"
                        class="form-control @error('product.description') is-invalid @enderror"
                        id="inputDescription"
                        rows="3"
                        autocomplete="off"
                        wire:model.defer="product.description"></textarea>
                    @error('product.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                @error('picture') <span class="text-error">{{ $message }}</span> @enderror
                <div wire:loading wire:target="picture">Uploading...</div>
                <div wire:loading.remove wire:target="picture">
                @if($picture)
                    <div class="mb-3">
                        <img
                            src="{{ $picture->temporaryUrl() }}"
                            alt="Preview"
                            class="mb-3"
                            style="max-width: 300px; max-height: 150px">
                        <br>
                        <button
                            type="button"
                            class="btn btn-outline-danger btn-sm"
                            wire:click="$set('picture', null)">
                            Cancel
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
                                Remove
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
        <div class="form-row">
            <div class="col-md">
                <div class="form-group">
                    <label for="inputStockAmount">Stock amount</label>
                    <input
                        type="number"
                        min="0"
                        class="form-control @error('product.stock_amount') is-invalid @enderror"
                        id="inputStockAmount"
                        required
                        autocomplete="off"
                        wire:model.defer="product.stock_amount">
                    @error('product.stock_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    <label for="inputCustomerLimit">Limit per customer</label>
                    <input
                        type="number"
                        min="0"
                        class="form-control @error('product.customer_limit') is-invalid @enderror"
                        id="inputCustomerLimit"
                        autocomplete="off"
                        wire:model.defer="product.customer_limit">
                    @error('product.customer_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        <div class="d-flex justify-content-between mb-3">
            <a
                href="{{ route('backend.products') }}"
                class="btn btn-outline-primary">Back to products</a>
            <span>
                @if($product->exists && $product->orders->isEmpty())
                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        wire:target="delete"
                        wire:remove.attr="disabled"
                        wire:click="delete">
                        <x-bi-hourglass-split wire:loading wire:target="delete"/>
                        Delete
                    </button>
                @endif
                <button
                    type="submit"
                    class="btn btn-primary"
                    wire:target="submit"
                    wire:loading.attr="disabled">
                    <x-bi-hourglass-split wire:loading wire:target="submit"/>
                    Save
                </button>
            </span>
        </div>
    </form>
</div>
