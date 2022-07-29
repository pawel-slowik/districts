<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\Exception\NotFoundException;
use Districts\Application\Exception\ValidationException;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\Query\ListDistrictsQuery;
use Districts\DomainModel\Area;
use Districts\DomainModel\City;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\District;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\Name;
use Districts\DomainModel\PaginatedResult;
use Districts\DomainModel\Population;
use Districts\Infrastructure\NotFoundInRepositoryException;

class DistrictService
{
    public function __construct(
        private DistrictValidator $districtValidator,
        private DistrictRepository $districtRepository,
        private CityRepository $cityRepository,
    ) {
    }

    public function add(AddDistrictCommand $command): void
    {
        try {
            $city = $this->cityRepository->get($command->cityId);
        } catch (NotFoundInRepositoryException $exception) {
            throw (new ValidationException())->withErrors(["city"]);
        }
        $validationResult = $this->districtValidator->validate(
            $command->name,
            $command->area,
            $command->population,
        );
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city->addDistrict(
            new Name($command->name),
            new Area($command->area),
            new Population($command->population),
        );
        $this->cityRepository->update($city);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        $city = $this->getCityByDistrictId($command->id);
        $validationResult = $this->districtValidator->validate(
            $command->name,
            $command->area,
            $command->population,
        );
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city->updateDistrict(
            $command->id,
            new Name($command->name),
            new Area($command->area),
            new Population($command->population),
        );
        $this->cityRepository->update($city);
    }

    public function remove(RemoveDistrictCommand $command): void
    {
        $city = $this->getCityByDistrictId($command->id);
        $city->removeDistrict($command->id);
        $this->cityRepository->update($city);
    }

    public function list(ListDistrictsQuery $query): PaginatedResult
    {
        return $this->districtRepository->list($query->getOrdering(), $query->getFilter(), $query->getPagination());
    }

    public function get(GetDistrictQuery $query): District
    {
        try {
            return $this->districtRepository->get($query->getId());
        } catch (NotFoundInRepositoryException $exception) {
            throw new NotFoundException();
        }
    }

    private function getCityByDistrictId(int $districtId): City
    {
        try {
            return $this->cityRepository->getByDistrictId($districtId);
        } catch (NotFoundInRepositoryException $exception) {
            throw new NotFoundException();
        }
    }
}
