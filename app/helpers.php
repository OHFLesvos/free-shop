<?php

if (! function_exists('listTimezones')) {
    /**
     * Returns a list of all valid timezones, where the key is
     * the timezone identifier and the value is a human friendly timezone label.
     *
     * @return array
     */
    function listTimezones(): array
    {
        return collect(\DateTimeZone::listIdentifiers())
            ->mapWithKeys(fn ($tz) => [$tz => str_replace('_', ' ', $tz)])
            ->toArray();
    }
}

if (! function_exists('maskString')) {
    function maskString(string $value, int $limitStart = 2, int $limitEnd = 2)
    {
        return substr($value, 0, $limitStart) . str_repeat("*", strlen($value) - ($limitStart + $limitEnd)) . substr($value, -$limitEnd);
    }
}

if (! function_exists('randomNumberPadded')) {
    function randomNumberPadded(int $digits = 5)
    {
        assert($digits > 0, 'Digits must be greater than 0');
        return str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
    }
}
