<?php

namespace App\Exceptions;

class PhoneNumberBlockedByAdminException extends \Exception
{
    public function __construct(
        private string $phone
    ) {
        parent::__construct("The phone number $phone has been blocked by an administrator and cannot be used to send messages to.");
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
