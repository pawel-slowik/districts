<?php

declare(strict_types=1);

namespace Validator;

interface Validator
{
    public function validate(array $data): ValidationResult;
}
