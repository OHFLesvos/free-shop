<?php

namespace App\Listeners;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Events\Login;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Contracts\Role as RoleContract;

class EnsureAdminExists
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user instanceof User) {
            $adminRole = Role::findOrCreate(AuthServiceProvider::ADMINISTRATOR_ROLE);
            $administrators = User::role($adminRole)->get();
            if ($administrators->isEmpty()) {
                $this->assignAdmin($event->user, $adminRole);
            }
        }
    }

    private function assignAdmin(User $user, RoleContract $adminRole): void
    {
        $previousRoles = $user->getRoleNames()->toArray();
        $user->assignRole($adminRole);
        UserRolesChanged::dispatch($user, $previousRoles);
    }
}
