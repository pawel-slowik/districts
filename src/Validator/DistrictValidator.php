<?php

declare(strict_types=1);

namespace Districts\Validator;

use Districts\Service\CityIterator;

final class DistrictValidator
{
    private $cityIterator;

    public function __construct(CityIterator $cityIterator)
    {
        $this->cityIterator = $cityIterator;
    }

    public function validate(int $cityId, string $name, float $area, int $population): ValidationResult
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

    private function validateCity(int $cityId): bool
    {
        foreach ($this->cityIterator as $city) {
            if ($city->getId() === $cityId) {
                return true;
            }
        }
        return false;
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
