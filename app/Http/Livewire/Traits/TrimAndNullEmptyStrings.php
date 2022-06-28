<?php

namespace App\Http\Livewire\Traits;

trait TrimAndNullEmptyStrings
{
    /**
     * Trims empty strings to null.
     *
     * @param  string  $name the key
     * @param  mixed  $value the value
     * @return void
     */
    public function updatedTrimAndNullEmptyStrings(string $name, $value): void
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = $value === '' ? null : $value;
            data_set($this, $name, $value);
        }
    }
}
