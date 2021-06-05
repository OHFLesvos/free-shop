<?php

namespace App\View\Components\Backend\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Twilio\Rest\Api\V2010\Account\BalanceInstance;

class TwilioWidget extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        if (Auth::user()->can('view twilio balance')) {
            $sid = config('twilio-notification-channel.account_sid');
            $token = config('twilio-notification-channel.auth_token');
            if (filled($sid) && filled($token)) {
                $data = [];
                try {
                    $data['twilioBalance'] = $this->getTwilioBalance($sid, $token);
                } catch (\Twilio\Exceptions\TwilioException $ex) {
                    $data['error'] = $ex->getMessage();
                }
                return view('components.backend.dashboard.twilio-widget', $data);
            }
        }
        return '';
    }

    private function getTwilioBalance(?string $sid, ?string $token): BalanceInstance
    {
        $client = new \Twilio\Rest\Client($sid, $token);
        return $client->balance->fetch();
    }
}
