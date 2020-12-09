<div>
    <h1 class="mb-3">Edit Product {{ $product->name }}</h1>
    <div class="form-row">
        <div class="col-md">
            <div class="form-group">
                <label for="inputName">Name</label>
                <input
                    type="text"
                    class="form-control @error('product.name') is-invalid @enderror"
                    id="inputName"
                    required
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
    <form wire:submit.prevent="submit" class="mb-4">
        <div class="d-flex justify-content-between mb-3">
            <a
                href="{{ route('backend.products') }}"
                class="btn btn-outline-primary">Back to products</a>
            <button
                type="submit"
                class="btn btn-primary"
                wire:target="submit"
                wire:loading.attr="disabled">
                <x-bi-hourglass-split wire:loading wire:target="submit"/>
                Save
            </button>
        </div>
    </form>
</div>
