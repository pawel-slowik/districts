<?php

declare(strict_types=1);

namespace Districts\Editor\Application;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\CityRepository;
use Districts\Core\Domain\District;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Application\Query\GetDistrictQuery;
use Districts\Editor\Application\Query\ListDistrictsQuery;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\PaginatedResult;

final class DistrictService
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
        $validationResult = $this->districtValidator->validateUpdate($command);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = $this->districtRepository->get($command->id);
        $city = $district->getCity();
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
        $district = $this->districtRepository->get($command->id);
        $city = $district->getCity();
        $city->removeDistrict($district->getName());
        $this->cityRepository->update($city);
    }

    /**
     * @return PaginatedResult<District>
     */
    public function list(ListDistrictsQuery $query): PaginatedResult
    {
        return $this->districtRepository->listWithPagination($query->ordering, $query->pagination, $query->filter);
    }

    public function get(GetDistrictQuery $query): District
    {
        return $this->districtRepository->get($query->id);
    }
}
