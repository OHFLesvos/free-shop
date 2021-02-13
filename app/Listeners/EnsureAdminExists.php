<?php

namespace App\Listeners;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class EnsureAdminExists
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $adminRole = Role::firstOrCreate(['name' => AuthServiceProvider::ADMINISTRATOR_ROLE]);
        $administrators = User::role($adminRole)->get();
        if ($administrators->isEmpty()) {
            $this->assignAdmin($event->user, $adminRole);
        }
    }

    private function assignAdmin(User $user, Role $adminRole)
    {
        $user->assignRole($adminRole);

        Log::warning('Assigned administrator role to user.', [
            'event.kind' => 'event',
            'event.category' => 'iam',
            'event.type' => 'admin',
            'user.name' => $user->name,
            'user.email' => $user->email,
        ]);
    }
}
