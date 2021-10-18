<?php

namespace App\Verify;

class Result
{
    private bool $valid;

    private array $errors;

    private string $sid;

    /**
     * Result constructor.
     * @param mixed $value => string $sid | array $errors
     */
    public function __construct($value)
    {
        if (is_string($value)) {
            $this->sid = $value;
            $this->valid = true;
        } else if (is_array($value)) {
            $this->errors = $value;
            $this->valid = false;
        } else {
            throw new \InvalidArgumentException('Invalid argument: Only string or array allowed.');
        }
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSid(): string
    {
        return $this->sid;
    }
}
