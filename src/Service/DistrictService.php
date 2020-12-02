<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\DomainModel\Entity\District;
use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;
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

    public function get(int $id): District
    {
        try {
            return $this->districtRepository->get($id);
        } catch (RepositoryNotFoundException $exception) {
            throw new NotFoundException();
        }
    }

    public function add(AddDistrictCommand $command): void
    {
        $cityId = $command->getCityId();
        $name = $command->getName();
        $area = $command->getArea();
        $population = $command->getPopulation();
        $validationResult = $this->districtValidator->validate($cityId, $name, $area, $population);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = new District(
            $this->cityRepository->get($cityId),
            $name,
            $area,
            $population,
        );
        $this->districtRepository->add($district);
    }

    public function update(UpdateDistrictCommand $command): void
    {
        $district = $this->get($command->getId());
        $name = $command->getName();
        $area = $command->getArea();
        $population = $command->getPopulation();
        $validationResult = $this->districtValidator->validate(
            $district->getCity()->getId(),
            $name,
            $area,
            $population,
        );
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district->setName($name);
        $district->setArea($area);
        $district->setPopulation($population);
        $this->districtRepository->update($district);
    }

    public function remove(RemoveDistrictCommand $command): void
    {
        if (!$command->isConfirmed()) {
            return;
        }
        $this->districtRepository->remove($this->get($command->getId()));
    }

    public function list(DistrictOrdering $order, ?DistrictFilter $filter): array
    {
        return $this->districtRepository->list($order, $filter);
    }
}
