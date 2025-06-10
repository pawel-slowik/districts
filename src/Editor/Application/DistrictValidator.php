<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\CityRepository;
use Districts\Core\Domain\Exception\InvalidAreaException;
use Districts\Core\Domain\Exception\InvalidNameException;
use Districts\Core\Domain\Exception\InvalidPopulationException;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Domain\DistrictRepository;

readonly class DistrictValidator
{
    public function __construct(
        private CityRepository $cityRepository,
        private DistrictRepository $districtRepository,
    ) {
    }

    public function validateAdd(AddDistrictCommand $command): ValidationResult
    {
        $result = $this->validate($command);

        $city = $this->cityRepository->get($command->cityId);

        if (
            $this->validateName($command->name)
            && $city->hasDistrictWithName(new Name($command->name))
        ) {
            $result->addError("name");
        }

        return $result;
    }

    public function validateUpdate(UpdateDistrictCommand $command): ValidationResult
    {
        $result = $this->validate($command);

        $district = $this->districtRepository->get($command->id);

        if (
            $this->validateName($command->name)
            && !$district->getName()->equals(new Name($command->name))
            && $district->getCity()->hasDistrictWithName(new Name($command->name))
        ) {
            $result->addError("name");
        }

        return $result;
    }

    private function validate(AddDistrictCommand | UpdateDistrictCommand $command): ValidationResult
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
        } catch (InvalidNameException) {
            return false;
        }
    }

    private function validateArea(float $area): bool
    {
        try {
            new Area($area);
            return true;
        } catch (InvalidAreaException) {
            return false;
        }
    }

    private function validatePopulation(int $population): bool
    {
        try {
            new Population($population);
            return true;
        } catch (InvalidPopulationException) {
            return false;
        }
    }
}
