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
        $validationResult = $this->districtValidator->validateAdd($command);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city = $this->cityRepository->get($command->cityId);
        $city->addDistrict(
            new Name($command->name),
            new Area($command->area),
            new Population($command->population),
        );
        $this->cityRepository->update($city);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        try {
            $district = $this->districtRepository->get($command->id);
        } catch (NotFoundInRepositoryException) {
            throw new NotFoundException();
        }
        $city = $district->getCity();
        $validationResult = $this->districtValidator->validateUpdate($command);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $city->updateDistrict(
            $district->getName(),
            new Name($command->name),
            new Area($command->area),
            new Population($command->population),
        );
        $this->cityRepository->update($city);
    }

    public function remove(RemoveDistrictCommand $command): void
    {
        try {
            $district = $this->districtRepository->get($command->id);
        } catch (NotFoundInRepositoryException) {
            throw new NotFoundException();
        }
        $city = $district->getCity();
        $city->removeDistrict($district->getName());
        $this->cityRepository->update($city);
    }

    /**
     * @return PaginatedResult<District>
     */
    public function list(ListDistrictsQuery $query): PaginatedResult
    {
        return $this->districtRepository->list($query->ordering, $query->filter, $query->pagination);
    }

    public function get(GetDistrictQuery $query): District
    {
        try {
            return $this->districtRepository->get($query->id);
        } catch (NotFoundInRepositoryException) {
            throw new NotFoundException();
        }
    }
}
