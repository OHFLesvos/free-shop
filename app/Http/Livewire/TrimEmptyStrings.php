<?php

namespace App\Http\Livewire;

trait TrimEmptyStrings
{
    public function updatedTrimEmptyStrings($name, $value)
    {
        if (is_string($value)) {
            $value = trim($value);

            data_set($this, $name, $value);
        }
    }
}
