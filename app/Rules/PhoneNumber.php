<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Brick\PhoneNumber\PhoneNumber as PhoneNumberLib;
use Brick\PhoneNumber\PhoneNumberParseException;

class PhoneNumber implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $number = PhoneNumberLib::parse($value);
            return $number->isValidNumber();
        }
        catch (PhoneNumberParseException $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The phone number is invalid.';
    }
}
