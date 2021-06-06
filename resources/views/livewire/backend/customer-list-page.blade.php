<div>
    @if(session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="mb-3">
        <div class="input-group">
            <input
                type="search"
                wire:model.debounce.500ms="search"
                placeholder="Search customers (Name, ID number, phone, remarks)..."
                wire:keydown.escape="$set('search', '')"
                class="form-control"/>
            <span class="input-group-text" wire:loading wire:target="search">
                <x-spinner/>
            </span>
            @empty($search)
                <span class="input-group-text" wire:loading.remove wire:target="search">
                    {{ $customers->total() }} total
                </span>
            @else
                <span class="input-group-text @if($customers->isEmpty()) bg-warning @else bg-success text-light @endif" wire:loading.remove wire:target="search">
                    {{ $customers->total() }} results
                </span>
            @endif
        </div>
    </div>
    @if($tags->isNotEmpty())
        <div class="overflow-auto mb-3">
            @foreach($tags as $t)
                <button
                    wire:click="setTag('{{ $t->slug }}')"
                    class="btn btn-sm me-1 @if($t->slug == $tag) btn-success @else btn-info @endif">
                    {{ $t->name }}
                </button>
            @endforeach
            @if(filled($tag))
                <button wire:click="setTag('')" class="btn btn-sm btn-secondary">
                    <x-icon icon="times"/>
                </button>
            @endif
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm table-hover">
            <thead>
                <th>
                    Name
                    <a href="#" wire:click="sortBy('name')"><x-icon icon="sort"/></a>
                </th>
                <th>ID Number</th>
                <th>Phone</th>
                <th class="text-end">Orders</th>
                <th class="text-end">Credit</th>
                <th class="fit">
                    Registered
                    <a href="#" wire:click="sortBy('created_at')"><x-icon icon="sort"/></a>
                </th>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr
                        @can('view', $customer) onclick="window.location='{{ route('backend.customers.show', $customer) }}'" @endcan
                        @can('view', $customer) class="cursor-pointer" @endcan>
                        <td>{{ $customer->name }}</td>
                        <td>
                            @if($customer->is_disabled)
                                <x-icon icon="user-lock" class="text-danger" title="Disabled" />
                            @endif
                            {{ $customer->id_number }}
                        </td>
                        <td><x-phone-info :value="$customer->phone"/></td>
                        <td class="text-end">{{ $customer->orders()->count() }}</td>
                        <td class="text-end">{{ $customer->credit }}</td>
                        <td class="fit">
                            {{ $customer->created_at->toUserTimezone()->isoFormat('LLLL') }}<br>
                            <small>{{ $customer->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <em>
                                @if(filled($search))
                                    No customers found for term '{{ $search }}'.
                                @else
                                    No customers found.
                                @endif
                            </em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="row">
            <div class="col overflow-auto">
                {{ $customers->onEachSide(2)->links() }}
            </div>
            <div class="col-sm-auto">
                <small>Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers</small>
            </div>
        </div>
    @endif
    @can('create', App\Model\Customer::class)
        <p>
            <a
                href="{{ route('backend.customers.create') }}"
                class="btn btn-primary">
                Register
            </a>
        </p>
    @endcan
</div>
