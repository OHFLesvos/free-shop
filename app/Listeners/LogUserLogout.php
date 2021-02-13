<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogUserLogout
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
     * @param  object  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $this->writeToLog($event->user);
    }

    private function writeToLog(User $user)
    {
        Log::info('User logged out.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'end',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'user.roles' => $user->getRoleNames(),
            'client.ip' => $this->request->ip(),
            'url.original' => $this->request->url(),
            'client.session.id' => $this->request->session()->getId(),
        ]);
    }
}
