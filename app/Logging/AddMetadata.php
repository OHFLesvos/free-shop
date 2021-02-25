<?php

namespace App\Logging;

use donatj\UserAgent\UserAgentParser;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class AddMetadata
{
    protected ?Request $request;

    private UserAgentParser $uap;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
        $this->uap = new UserAgentParser();
    }

    public function __invoke(Logger $logger)
    {
        if ($this->request) {
            foreach ($logger->getHandlers() as $handler) {
                $handler->pushProcessor([$this, 'processLogRecord']);
            }
        }
    }

    public function processLogRecord(array $record): array
    {
        $ua = $this->uap->parse($this->request->userAgent());
        $record['extra'] += [
            'client.ip' => $this->request->getClientIp(),
            'client.session.id' => $this->request->session()->getId(),
            'client.locale' => app()->getLocale(),
            'user_agent.original' => $this->request->userAgent(),
            'user_agent.name' => $ua->browser(),
            'user_agent.version' => $ua->browserVersion(),
            'user_agent.device.name' => $ua->platform(),
            'url.domain' => $this->request->getHost(),
            'url.path' => $this->request->path(),
            'url.full' => $this->request->fullUrl(),
            'app.name' => config('app.name'),
            'app.environment' => config('app.env'),
        ];
        $user = $this->request->user();
        if (isset($user)) {
            $record['extra'] += [
                'client.user.name' => $user->name,
                'client.user.email' => $user->email,
                'client.user.roles' => $user->getRoleNames()->toArray(),
            ];
        }

        return $record;
    }
}
