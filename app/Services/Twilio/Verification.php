<?php

namespace App\Services\Twilio;

use App\Verify\Result;
use App\Verify\Service;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Verification implements Service
{
    private Client $client;

    private string $verification_sid;

    /**
     * Verification constructor.
     *
     * @param $client
     * @param string|null $verification_sid
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct($client = null, string $verification_sid = null)
    {
        if ($client === null) {
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.auth_token');
            $client = new Client($sid, $token);
        }
        $this->client = $client;
        $this->verification_sid = $verification_sid ?: config('services.twilio.verification_sid');
    }

    /**
     * Start a phone verification process using Twilio Verify V2 API
     */
    public function startVerification(string $phone_number, string $channel): Result
    {
        try {
            $verification = $this->client->verify->v2->services($this->verification_sid)
                ->verifications
                ->create($phone_number, $channel);
            return new Result($verification->sid);
        } catch (TwilioException $exception) {
            return new Result([__("Verification failed to start: :message", ['message' => $exception->getMessage()])]);
        }
    }

    /**
     * Check verification code using Twilio Verify V2 API
     */
    public function checkVerification(string $phone_number, string $code): Result
    {
        try {
            $verification_check = $this->client->verify->v2->services($this->verification_sid)
                ->verificationChecks
                ->create($code, ['to' => $phone_number]);
            if ($verification_check->status === 'approved') {
                return new Result($verification_check->sid);
            }
            return new Result([__('Verification check failed: Invalid code.')]);
        } catch (TwilioException $exception) {
            return new Result([__("Verification check failed: :message", ['message' => $exception->getMessage()])]);
        }
    }
}
