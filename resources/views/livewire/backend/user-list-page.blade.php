<div>
    <div class="d-md-flex justify-content-between align-items-center">
        <h1 class="mb-3">Users</h1>
    </div>
    @if(session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="form-group">
        <div class="input-group">
        <input
            type="search"
            wire:model.debounce.500ms="search"
            placeholder="Search users..."
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
        <table class="table table-bordered bg-white shadow-sm">
            <caption>{{ $users->total() }} users found</caption>
            <thead>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Last login</th>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr >
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><x-date-time-info :value="$user->last_login_at"/></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            <em>
                                @if(filled($search))
                                    No users found for term '{{ $search }}'.
                                @else
                                    No users found.
                                @endif
                            </em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
