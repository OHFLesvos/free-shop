<?php

namespace App\Listeners;

use App\Models\User;
use donatj\UserAgent\UserAgentParser;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
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
    public function handle(Failed $event)
    {
        $this->writeToLog($event->user);
    }

    private function writeToLog(User $user)
    {
        $parser = new UserAgentParser();
        $ua = $parser->parse($this->request->userAgent());
        Log::warning('Successful user login.', [
            'event.kind' => 'event',
            'event.category' => 'authentication',
            'event.type' => 'start',
            'event.outcome' => 'failure',
            'user.name' => $user->name,
            'user.email' => $user->email,
            'client.ip' => $this->request->ip(),
            'user_agent.original' => $this->request->userAgent(),
            'user_agent.name' => $ua->browser(),
            'user_agent.version' => $ua->browserVersion(),
            'user_agent.device.name' => $ua->platform(),
            'url.original' => $this->request->url(),
            'url.domain' => $this->request->getHost(),
            'client.session.id' => $this->request->session()->getId(),
            'service.name' => config('app.name'),
        ]);
    }
}
