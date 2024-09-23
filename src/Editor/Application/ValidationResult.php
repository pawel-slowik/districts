<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

class ValidationResult
{
    /**
     * @var string[]
     */
    private array $errors = [];

    public function isOk(): bool
    {
        return count($this->errors) === 0;
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
