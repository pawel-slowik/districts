<?php

declare(strict_types=1);

namespace Districts\DomainModel;

class ValidationResult
{
    private $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    public function isOk(): bool
    {
        return count($this->errors) === 0;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
