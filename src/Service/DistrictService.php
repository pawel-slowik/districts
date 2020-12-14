<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\Query\ListDistrictsQuery;
use Districts\DomainModel\Entity\District;
use Districts\Repository\DistrictRepository;
use Districts\Repository\NotFoundException as RepositoryNotFoundException;
use Districts\Validator\DistrictValidator;

use Districts\Repository\CityRepository;

class DistrictService
{
    private $districtRepository;

    private $districtValidator;

    private $cityRepository;

    public function __construct(
        DistrictRepository $districtRepository,
        DistrictValidator $districtValidator,
        CityRepository $cityRepository
    ) {
        $this->districtRepository = $districtRepository;
        $this->districtValidator = $districtValidator;
        $this->cityRepository = $cityRepository;
    }

    public function add(AddDistrictCommand $command): void
    {
        try {
            $city = $this->cityRepository->get($command->getCityId());
        } catch (RepositoryNotFoundException $exception) {
            throw (new ValidationException())->withErrors(["city"]);
        }
        $district = $city->addDistrict(
            $this->districtValidator,
            $command->getName(),
            $command->getArea(),
            $command->getPopulation(),
        );
        $this->cityRepository->update($city);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        $district = $this->getById($command->getId());
        $city = $district->getCity();
        $district = $city->updateDistrict(
            $this->districtValidator,
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
        $this->districtRepository->remove($this->getById($command->getId()));
    }

    public function list(ListDistrictsQuery $query): array
    {
        return $this->districtRepository->list($query->getOrdering(), $query->getFilter());
    }

    public function get(GetDistrictQuery $query): District
    {
        return $this->getById($query->getId());
    }

    private function getById(int $id): District
    {
        try {
            return $this->districtRepository->get($id);
        } catch (RepositoryNotFoundException $exception) {
            throw new NotFoundException();
        }
    }
}
