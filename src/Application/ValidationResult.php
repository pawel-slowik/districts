<?php

declare(strict_types=1);

namespace Districts\Application;

class ValidationResult
{
    private array $errors = [];

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
