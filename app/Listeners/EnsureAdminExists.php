<?php

namespace App\Listeners;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Events\Login;
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
        $previousRoles = $user->getRoleNames()->toArray();
        $user->assignRole($adminRole);
        UserRolesChanged::dispatch($user, $previousRoles);
    }
}
