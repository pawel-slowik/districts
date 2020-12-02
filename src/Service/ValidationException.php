<?php

declare(strict_types=1);

namespace Districts\Service;

class ValidationException extends \RuntimeException
{
    private $errors;

    public function withErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
