<?php

namespace App\Logging;

use App\Models\User;
use donatj\UserAgent\UserAgentParser;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;

class AddMetadata
{
    protected ?Request $request;

    private UserAgentParser $userAgentParser;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
        $this->userAgentParser = new UserAgentParser();
    }

    public function __invoke(Logger $logger): void
    {
        if ($this->request) {
            foreach ($logger->getHandlers() as $handler) {
                $handler->pushProcessor([$this, 'processLogRecord']);
            }
        }
    }

    public function processLogRecord(array $record): array
    {
        $userAgent = $this->userAgentParser->parse($this->request->userAgent());
        $record['extra'] += [
            'client.ip' => $this->request->getClientIp(),
            'client.session.id' => session()->getId(),
            'client.locale' => app()->getLocale(),
            'user_agent.original' => $this->request->userAgent(),
            'user_agent.name' => $userAgent->browser(),
            'user_agent.version' => $userAgent->browserVersion(),
            'user_agent.device.name' => $userAgent->platform(),
            'url.domain' => $this->request->getHost(),
            'url.path' => $this->request->path(),
            'url.full' => $this->request->fullUrl(),
            'app.name' => config('app.name'),
            'app.environment' => config('app.env'),
        ];
        $user = $this->request->user();
        if (isset($user) && $user instanceof User) {
            $record['extra'] += [
                'client.user.name' => $user->name,
                'client.user.email' => $user->email,
                'client.user.roles' => $user->getRoleNames()->toArray(),
            ];
        }

        return $record;
    }
}
