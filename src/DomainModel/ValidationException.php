<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use RuntimeException;

class ValidationException extends RuntimeException
{
    /**
     * @var string[]
     */
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
