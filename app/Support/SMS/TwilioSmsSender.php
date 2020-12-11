<?php

namespace App\Support\SMS;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class TwilioSmsSender implements SmsSender
{
    /**
     * Twilio account SID
     *
     * @var string
     */
    private $sid;

    /**
     * Twilio auth token
     *
     * @var string
     */
    private $auth_token;

    /**
     * Twilio number
     *
     * @var string
     */
    private $number;

    public function __construct()
    {
        $this->sid = config("services.twilio.sid");
        $this->auth_token = config("services.twilio.auth_token");
        $this->number = config("services.twilio.number");
    }

    function isConfigured(): bool
    {
        return filled($this->sid) && filled($this->auth_token) && filled($this->number);
    }

    /**
     * Sends sms to user using Twilio's programmable sms client
     *
     * @param String $message Body of SMS
     * @param String $recipient string phone number of recepient
     */
    public function sendMessage($message, $recipient)
    {
        $client = new Client($this->sid, $this->auth_token);
        try {
            $client->messages->create($recipient, [
                'from' => $this->number,
                'body' => $message]
            );
        } catch (RestException $ex) {
            Log::error($ex->getMessage());
        }
    }
}
