<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class UpdateUserLastLogin
{
    protected Request $request;

    /**
     * Create the event listener.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($event->user instanceof User) {
            $this->updateLastLogin($event->user);
        }
    }

    private function updateLastLogin(User $user): void
    {
        $user->last_login_at = now();
        $user->last_login_ip = $this->request->ip();
        $user->last_login_user_agent = $this->request->userAgent();
        $user->save();
    }
}
