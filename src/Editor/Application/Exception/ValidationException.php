<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Exception;

use RuntimeException;

class ValidationException extends RuntimeException
{
    /**
     * @var string[]
     */
    private array $errors = [];

    /**
     * @param string[] $errors
     */
    public function withErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
