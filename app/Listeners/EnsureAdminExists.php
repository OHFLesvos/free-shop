<?php

namespace App\Listeners;

use App\Models\User;
use App\Providers\AuthServiceProvider;
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
    public function handle($event)
    {
        $adminRole = Role::firstOrCreate(['name' => AuthServiceProvider::ADMINISTRATOR_ROLE]);
        $administrators = User::role($adminRole)->get();
        if ($administrators->isEmpty()) {
            $event->user->assignRole($adminRole);
            Log::warning('Assigned administrator role to user.', [
                'event.category' => 'iam',
                'event.type' => 'admin',
                'user.name' => $event->user->name,
                'user.email' => $event->user->email,
                'group.name' => $adminRole->name,
            ]);
        }
    }
}
