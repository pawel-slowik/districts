<?php

declare(strict_types=1);

namespace Validator;

final class DistrictValidator
{
    /**
     * @param scalar $name
     * @param scalar $area
     * @param scalar $population
     */
    public function validate($name, $area, $population): ValidationResult
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

    /**
     * @param scalar $name
     */
    private function validateName($name): bool
    {
        if (!is_string($name)) {
            return false;
        }
        if ($name === "") {
            return false;
        }
        return true;
    }

    /**
     * @param scalar $area
     */
    private function validateArea($area): bool
    {
        if (!is_int($area) && !is_float($area)) {
            return false;
        }
        if ($area <= 0) {
            return false;
        }
        return true;
    }

    /**
     * @param scalar $population
     */
    private function validatePopulation($population): bool
    {
        if (!is_int($population)) {
            return false;
        }
        if ($population <= 0) {
            return false;
        }
        return true;
    }
}
