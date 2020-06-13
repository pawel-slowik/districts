<?php

declare(strict_types=1);

namespace Validator;

class DistrictValidator implements Validator
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
        $name = $data["name"];
        if (!is_string($name)) {
            $result->addError("name");
            return;
        }
        if ($name === "") {
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
        $area = $data["area"];
        if (!is_int($area) && !is_float($area)) {
            $result->addError("area");
            return;
        }
        if ($area <= 0) {
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
        $population = $data["population"];
        if (!is_int($population)) {
            $result->addError("population");
            return;
        }
        if ($population <= 0) {
            $result->addError("population");
            return;
        }
    }
}
