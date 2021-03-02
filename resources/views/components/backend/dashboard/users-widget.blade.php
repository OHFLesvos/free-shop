@can('viewAny', App\Models\User::class)
    <div class="col">
        <x-card>
            <x-slot name="title">
                <a href="{{ route('backend.users') }}" class="text-body text-decoration-none">Users</a>
            </x-slot>
            {{ $registeredUsers }} users registered
        </x-card>
    </div>
@endcan