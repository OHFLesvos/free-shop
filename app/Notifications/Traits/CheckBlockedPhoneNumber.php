<?php

namespace App\Notifications\Traits;

use App\Exceptions\PhoneNumberBlockedByAdminException;
use App\Models\BlockedPhoneNumber;

trait CheckBlockedPhoneNumber
{
    /**
     * Checks if the given phone number is marked as blocked
     *
     * @param string $phone the phone number
     * @throws PhoneNumberBlockedByAdminException in case the phone number is blocked
     * @return void
     */
    function checkBlockedPhoneNumber(string $phone): void
    {
        if (BlockedPhoneNumber::where('phone', $phone)->exists()) {
            throw new PhoneNumberBlockedByAdminException($phone);
        }
    }
}
