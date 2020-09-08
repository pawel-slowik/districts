<?php

declare(strict_types=1);

namespace Validator;

use Service\CityIterator;

final class DistrictValidator
{
    private $cityIterator;

    public function __construct(CityIterator $cityIterator)
    {
        $this->cityIterator = $cityIterator;
    }

    /**
     * @param scalar $cityId
     * @param scalar $name
     * @param scalar $area
     * @param scalar $population
     */
    public function validate($cityId, $name, $area, $population): ValidationResult
    {
        $result = new ValidationResult();
        if (!$this->validateCity($cityId)) {
            $result->addError("city");
        }
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
     * @param scalar $cityId
     */
    private function validateCity($cityId): bool
    {
        foreach ($this->cityIterator as $city) {
            if ($city->getId() === $cityId) {
                return true;
            }
        }
        return false;
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
