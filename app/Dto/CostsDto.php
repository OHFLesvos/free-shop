<?php

namespace App\Dto;

use Spatie\DataTransferObject\DataTransferObject;
use Stringable;

class CostsDto extends DataTransferObject implements Stringable
{
    public int $currency_id;

    public string $currency_name;

    public int $value;

    public function __toString(): string
    {
        return "{$this->value} {$this->currency_name}";
    }
}
