@can('viewAny', App\Models\Product::class)
    <div class="col">
        <x-card>
            <x-slot name="title">
                <a href="{{ route('backend.products') }}" class="text-body text-decoration-none">Products</a>
            </x-slot>
            {{ $availableProducts }} products available
        </x-card>
    </div>
@endcan