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
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\DomainModel\PaginatedResult;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use Districts\Infrastructure\NotFoundInRepositoryException;

class DistrictService
{
    private DistrictValidator $districtValidator;

    private DistrictRepository $districtRepository;

    private CityRepository $cityRepository;

    public function __construct(
        DistrictValidator $districtValidator,
        DistrictRepository $districtRepository,
        CityRepository $cityRepository
    ) {
        $this->districtValidator = $districtValidator;
        $this->districtRepository = $districtRepository;
        $this->cityRepository = $cityRepository;
    }

    public function add(AddDistrictCommand $command): void
    {
        try {
            $city = $this->cityRepository->get($command->getCityId());
        } catch (NotFoundInRepositoryException $exception) {
            throw (new ValidationException())->withErrors(["city"]);
        }
        $validationResult = $this->districtValidator->validate(
            $command->getName(),
            $command->getArea(),
            $command->getPopulation(),
        );
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city->addDistrict(
            new Name($command->getName()),
            new Area($command->getArea()),
            new Population($command->getPopulation()),
        );
        $this->cityRepository->update($city);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        $city = $this->getCityByDistrictId($command->getId());
        $validationResult = $this->districtValidator->validate(
            $command->getName(),
            $command->getArea(),
            $command->getPopulation(),
        );
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city->updateDistrict(
            $command->getId(),
            new Name($command->getName()),
            new Area($command->getArea()),
            new Population($command->getPopulation()),
        );
        $this->cityRepository->update($city);
    }

    public function remove(RemoveDistrictCommand $command): void
    {
        $city = $this->getCityByDistrictId($command->getId());
        $city->removeDistrict($command->getId());
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
