<?php

namespace App\Services;

use App\Exceptions\OtpTokenThrottledException;
use App\Models\OneTimePassword;

class OtpProvider
{
    private int $lifetime;
    private int $tokenLength;
    private int $maxTries;

    public function __construct(int $lifetime = 60, int $tokenLength = 4, int $maxTries = 3)
    {
        $this->lifetime = $lifetime;
        $this->tokenLength = $tokenLength;
        $this->maxTries = $maxTries;
    }

    public function getTokenLength(): int
    {
        return $this->tokenLength;
    }

    public function create($key): string
    {
        $oldOtp = OneTimePassword::where('key', $key)->first();
        if ($oldOtp != null) {
            $readyIn = $oldOtp->created_at->clone()->addSeconds($this->lifetime);
            if ($readyIn->lte(now())) {
                $oldOtp->delete();
            } else {
                throw new OtpTokenThrottledException($key, $readyIn);
            }
        }

        $otp = OneTimePassword::create([
            'key' => $key,
            'value' => randomNumberPadded($this->tokenLength),
        ]);
        return $otp->value;
    }

    public function exists($key): bool
    {
        return OneTimePassword::where('key', $key)->first() != null;
    }

    public function validate($key, $value): bool
    {
        $otp = OneTimePassword::where('key', $key)->first();
        if ($otp != null) {
            if ($otp->value == $value) {
                $otp->delete($key);
                return true;
            }
            $otp->increment('tries');
            if ($this->maxTries > 0 && $otp->tries >= $this->maxTries) {
                $otp->delete($key);
            }
        }
        return false;
    }

    public function delete($key)
    {
        OneTimePassword::where('key', $key)->delete();
    }
}
