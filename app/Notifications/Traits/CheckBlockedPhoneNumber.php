<?php

namespace App\Notifications\Traits;

use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\BlockedPhoneNumber;

trait CheckBlockedPhoneNumber
{
    function checkBlockedPhoneNumber($phone)
    {
        if (BlockedPhoneNumber::where('phone', $phone)->exists()) {
            throw new PhoneNumberBlockedByAdminException($phone);
        }
    }
}
