<?php

namespace App\Http\Livewire\Traits;

trait TrimEmptyStrings
{
    /**
     * Trims empty strings.
     *
     * @param  string  $name the key
     * @param  mixed  $value the value
     */
    public function updatedTrimEmptyStrings(string $name, $value): void
    {
        if (is_string($value)) {
            $value = trim($value);
            data_set($this, $name, $value);
        }
    }
}
