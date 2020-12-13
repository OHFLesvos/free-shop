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
