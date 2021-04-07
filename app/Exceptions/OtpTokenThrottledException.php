<?php

namespace App\Exceptions;

use Carbon\Carbon;

class OtpTokenThrottledException extends \Exception
{
    private Carbon $readyIn;

    public function __construct (string $key, Carbon $readyIn)
    {
        parent::__construct("The one-time token for '$key' still exists. A new token can be created in " . $readyIn);
        $this->readyIn = $readyIn;
    }

    public function getReadyIn(): Carbon
    {
        return $this->readyIn;
    }
}
