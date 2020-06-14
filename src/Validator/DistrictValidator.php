<?php

declare(strict_types=1);

namespace Validator;

class DistrictValidator
{
    public function validate(array $data): ValidationResult
    {
        $result = new ValidationResult();
        $this->validateName($data, $result);
        $this->validateArea($data, $result);
        $this->validatePopulation($data, $result);
        return $result;
    }

    protected function validateName(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("name", $data)) {
            $result->addError("name");
            return;
        }
        if (!is_string($data["name"])) {
            $result->addError("name");
            return;
        }
        if ($data["name"] === "") {
            $result->addError("name");
            return;
        }
    }

    protected function validateArea(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("area", $data)) {
            $result->addError("area");
            return;
        }
        if (!is_int($data["area"]) && !is_float($data["area"])) {
            $result->addError("area");
            return;
        }
        if ($data["area"] <= 0) {
            $result->addError("area");
            return;
        }
    }

    protected function validatePopulation(array $data, ValidationResult $result): void
    {
        if (!array_key_exists("population", $data)) {
            $result->addError("population");
            return;
        }
        if (!is_int($data["population"])) {
            $result->addError("population");
            return;
        }
        if ($data["population"] <= 0) {
            $result->addError("population");
            return;
        }
    }
}
