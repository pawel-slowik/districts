<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\Exception\InvalidAreaException;
use Districts\Editor\Domain\Exception\InvalidNameException;
use Districts\Editor\Domain\Exception\InvalidPopulationException;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\Population;

class DistrictValidator
{
    public function validate(AddDistrictCommand | UpdateDistrictCommand $command): ValidationResult
    {
        $result = new ValidationResult();
        if (!$this->validateName($command->name)) {
            $result->addError("name");
        }
        if (!$this->validateArea($command->area)) {
            $result->addError("area");
        }
        if (!$this->validatePopulation($command->population)) {
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
