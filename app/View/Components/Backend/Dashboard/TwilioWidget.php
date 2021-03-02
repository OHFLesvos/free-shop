<?php

namespace App\View\Components\Backend\Dashboard;

use Illuminate\View\Component;

class TwilioWidget extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $sid = config('twilio-notification-channel.account_sid');
        $token = config('twilio-notification-channel.auth_token');
        if (isset($sid) && isset($token)) {
            $data = [];
            try {
                $data['twilioBalance'] = $this->getTwilioBalance($sid, $token);
            } catch (\Twilio\Exceptions\TwilioException $ex) {
                $data['error'] = $ex->getMessage();
            }
            return view('components.backend.dashboard.twilio-widget', $data);
        }
        return null;
    }

    private function getTwilioBalance($sid, $token)
    {
        $client = new \Twilio\Rest\Client($sid, $token);
        return $client->balance->fetch();
    }
}
