<?php

namespace App\Verify;

interface Service
{
    /**
     * Start a phone verification process using an external service
     */
    public function startVerification(string $phone_number, string $channel): Result;

    /**
     * Check verification code using an external service
     */
    public function checkVerification(string $phone_number, string $code): Result;
}
