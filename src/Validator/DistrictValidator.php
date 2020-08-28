<?php

declare(strict_types=1);

namespace Validator;

class DistrictValidator
{
    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();
        if (!$this->validateName($data)) {
            $result->addError("name");
        }
        if (!$this->validateArea($data)) {
            $result->addError("area");
        }
        if (!$this->validatePopulation($data)) {
            $result->addError("population");
        }
        return $result;
    }

    protected function validateName(array $data): bool
    {
        if (!array_key_exists("name", $data)) {
            return false;
        }
        if (!is_string($data["name"])) {
            return false;
        }
        if ($data["name"] === "") {
            return false;
        }
        return true;
    }

    protected function validateArea(array $data): bool
    {
        if (!array_key_exists("area", $data)) {
            return false;
        }
        if (!is_int($data["area"]) && !is_float($data["area"])) {
            return false;
        }
        if ($data["area"] <= 0) {
            return false;
        }
        return true;
    }

    protected function validatePopulation(array $data): bool
    {
        if (!array_key_exists("population", $data)) {
            return false;
        }
        if (!is_int($data["population"])) {
            return false;
        }
        if ($data["population"] <= 0) {
            return false;
        }
        return true;
    }
}
