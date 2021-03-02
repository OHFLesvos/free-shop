@can('viewAny', App\Models\Customer::class)
    <div class="col">
        <x-card>
            <x-slot name="title">
                <a href="{{ route('backend.customers') }}" class="text-body text-decoration-none">Customers</a>
            </x-slot>
            {{ $registeredCustomers }} registered customers
        </x-card>
    </div>
@endcan