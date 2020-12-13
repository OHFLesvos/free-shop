<div>
    <div class="d-md-flex justify-content-between align-items-center">
        <h1 class="mb-3">Customers</h1>
        <a
            href="{{ route('backend.customers.create') }}"
            class="btn btn-primary mb-3">Register</a>
    </div>
    <div class="form-group">
        <div class="input-group">
        <input
            type="search"
            wire:model.debounce.500ms="search"
            placeholder="Search customers..."
            wire:keydown.escape="$set('search', '')"
            class="form-control"/>
            <div class="input-group-append" >
                <span class="input-group-text" wire:loading wire:target="search">
                    <x-spinner/>
                </span>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white shadow-sm">
            <caption>{{ $customers->total() }} customers found</caption>
            <thead>
                <th>Name</th>
                <th>ID Number</th>
                <th>Phone</th>
                <th>Orders</th>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr data-href="{{ route('backend.customers.show', $customer) }}">
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->id_number }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->orders()->count() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <em>No customers found.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $customers->links() }}
</div>
