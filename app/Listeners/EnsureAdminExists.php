<?php

namespace App\Listeners;

use App\Events\UserRolesChanged;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\Events\Login;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Models\Role;

class EnsureAdminExists
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user instanceof User) {
            $adminRole = Role::findOrCreate(AuthServiceProvider::ADMINISTRATOR_ROLE);
            if (! User::role($adminRole)->exists()) {
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
