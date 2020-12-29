<?php

declare(strict_types=1);

namespace Districts\DomainModel;

class DistrictValidator
{
    public function validate(string $name, float $area, int $population): ValidationResult
    {
        $result = new ValidationResult();
        if (!$this->validateName($name)) {
            $result->addError("name");
        }
        if (!$this->validateArea($area)) {
            $result->addError("area");
        }
        if (!$this->validatePopulation($population)) {
            $result->addError("population");
        }
        return $result;
    }

    private function validateName(string $name): bool
    {
        if ($name === "") {
            return false;
        }
        return true;
    }

    private function validateArea(float $area): bool
    {
        if ($area <= 0) {
            return false;
        }
        return true;
    }

    private function validatePopulation(int $population): bool
    {
        if ($population <= 0) {
            return false;
        }
        return true;
    }
}
