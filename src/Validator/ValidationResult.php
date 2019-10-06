<?php

declare(strict_types=1);

namespace Validator;

class ValidationResult
{
    protected $errors;

    protected $validatedData;

    public function __construct()
    {
        $this->errors = [];
        $this->validatedData = [];
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

    public function addValidatedData(string $key, $value): void
    {
        $this->validatedData[$key] = $value;
    }

    public function getValidatedData(): array
    {
        if (!$this->isOk()) {
            // should only be called after the validation has passed
            throw new \LogicException();
        }
        return $this->validatedData;
    }
}
