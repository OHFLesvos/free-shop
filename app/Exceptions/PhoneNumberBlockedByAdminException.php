<?php

namespace App\Exceptions;

class PhoneNumberBlockedByAdminException extends \Exception
{
    private string $phone;

    public function __construct (string $phone)
    {
        parent::__construct("The phone number '$phone' has been blocked by an administrator and cannot be used to send messages to.");
        $this->phone = $phone;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
