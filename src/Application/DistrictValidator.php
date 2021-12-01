<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\Area;
use Districts\DomainModel\Exception\InvalidAreaException;
use Districts\DomainModel\Exception\InvalidNameException;
use Districts\DomainModel\Exception\InvalidPopulationException;
use Districts\DomainModel\Name;
use Districts\DomainModel\Population;

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
        try {
            new Name($name);
            return true;
        } catch (InvalidNameException $exception) {
            return false;
        }
    }

    private function validateArea(float $area): bool
    {
        try {
            new Area($area);
            return true;
        } catch (InvalidAreaException $exception) {
            return false;
        }
    }

    private function validatePopulation(int $population): bool
    {
        try {
            new Population($population);
            return true;
        } catch (InvalidPopulationException $exception) {
            return false;
        }
    }
}
