<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\Query\ListDistrictsQuery;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\Repository\DistrictRepository;
use Districts\Repository\NotFoundException as RepositoryNotFoundException;

use Districts\Repository\CityRepository;

class DistrictService
{
    private $districtRepository;

    private $cityRepository;

    public function __construct(
        DistrictRepository $districtRepository,
        CityRepository $cityRepository
    ) {
        $this->districtRepository = $districtRepository;
        $this->cityRepository = $cityRepository;
    }

    public function add(AddDistrictCommand $command): void
    {
        try {
            $city = $this->cityRepository->get($command->getCityId());
        } catch (RepositoryNotFoundException $exception) {
            throw (new ValidationException())->withErrors(["city"]);
        }
        $city->addDistrict(
            $command->getName(),
            $command->getArea(),
            $command->getPopulation(),
        );
        $this->cityRepository->update($city);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        $city = $this->getCityByDistrictId($command->getId());
        $city->updateDistrict(
            $command->getId(),
            $command->getName(),
            $command->getArea(),
            $command->getPopulation(),
        );
        $this->cityRepository->update($city);
    }

    public function remove(RemoveDistrictCommand $command): void
    {
        if (!$command->isConfirmed()) {
            return;
        }
        $city = $this->getCityByDistrictId($command->getId());
        $city->removeDistrict($command->getId());
        $this->cityRepository->update($city);
    }

    public function list(ListDistrictsQuery $query): array
    {
        return $this->districtRepository->list($query->getOrdering(), $query->getFilter());
    }

    public function get(GetDistrictQuery $query): District
    {
        try {
            return $this->districtRepository->get($query->getId());
        } catch (RepositoryNotFoundException $exception) {
            throw new NotFoundException();
        }
    }

    private function getCityByDistrictId(int $districtId): City
    {
        try {
            return $this->cityRepository->getByDistrictId($districtId);
        } catch (RepositoryNotFoundException $exception) {
            throw new NotFoundException();
        }
    }
}
