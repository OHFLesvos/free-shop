<div>
    @if(session()->has('message'))
        <x-alert type="success" dismissible>{{ session()->get('message') }}</x-alert>
    @endif
    <div class="mb-3">
        <div class="input-group">
            <input
                type="search"
                wire:model.debounce.500ms="search"
                placeholder="Search users..."
                wire:keydown.escape="$set('search', '')"
                class="form-control"/>
            <span class="input-group-text" wire:loading wire:target="search">
                <x-spinner/>
            </span>
            @empty($search)
                <span class="input-group-text" wire:loading.remove wire:target="search">
                    {{ $users->total() }} total
                </span>
            @else
                <span class="input-group-text @if($users->isEmpty()) bg-warning @else bg-success text-light @endif" wire:loading.remove wire:target="search">
                    {{ $users->total() }} results
                </span>
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm @can('manage users') table-hover @endcan">
            <thead>
                <th colspan="2">Name</th>
                <th>E-Mail</th>
                <th>Roles</th>
                <th>Last login</th>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr
                        @can('update', $user) onclick="window.location='{{ route('backend.users.edit', $user) }}'" @endcan
                        class="@can('update', $user) cursor-pointer @endcan ">
                        <td class="fit">
                            @isset($user->avatar)
                                <img
                                    src="{{ storage_url($user->avatar) }}"
                                    alt="Avatar"
                                    class="align-top rounded-circle"
                                    height="24"
                                    width="24"/>
                            @endisset
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </td>
                        <td>
                            {{ $user->getRoleNames()->join(', ') }}
                        </td>
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
    @if($users->hasPages())
        <div class="row">
            <div class="col">
                {{ $users->links() }}
            </div>
            <div class="col-auto">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
        </div>
    @endif
</div>
