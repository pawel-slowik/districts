<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\Exception\InvalidAreaException;
use Districts\Editor\Domain\Exception\InvalidNameException;
use Districts\Editor\Domain\Exception\InvalidPopulationException;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\Population;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;

class DistrictValidator
{
    public function __construct(
        private CityRepository $cityRepository,
        private DistrictRepository $districtRepository,
    ) {
    }

    public function validateAdd(AddDistrictCommand $command): ValidationResult
    {
        $result = $this->validate($command);

        try {
            $city = $this->cityRepository->get($command->cityId);
        } catch (NotFoundInRepositoryException $exception) {
            $result->addError("city");
        }

        if (
            isset($city)
            && $this->validateName($command->name)
            && $city->hasDistrictWithName(new Name($command->name))
        ) {
            $result->addError("name");
        }

        return $result;
    }

    public function validateUpdate(UpdateDistrictCommand $command): ValidationResult
    {
        $result = $this->validate($command);

        try {
            $district = $this->districtRepository->get($command->id);
        } catch (NotFoundInRepositoryException $exception) {
            // pass
        }

        if (
            isset($district)
            && $this->validateName($command->name)
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
