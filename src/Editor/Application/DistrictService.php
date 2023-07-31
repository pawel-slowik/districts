<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\Exception\NotFoundException;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Application\Query\GetDistrictQuery;
use Districts\Editor\Application\Query\ListDistrictsQuery;
use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\District;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Population;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;

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
        return $this->districtRepository->list($query->ordering, $query->filter, $query->pagination);
    }

    public function get(GetDistrictQuery $query): District
    {
        try {
            return $this->districtRepository->get($query->id);
        } catch (NotFoundInRepositoryException $exception) {
            throw new NotFoundException();
        }
    }

    private function getCityByDistrictId(int $districtId): City
    {
        try {
            $district = $this->districtRepository->get($districtId);
        } catch (NotFoundInRepositoryException $exception) {
            throw new NotFoundException();
        }
        return $district->getCity();
    }
}
