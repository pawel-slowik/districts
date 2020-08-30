<?php

declare(strict_types=1);

namespace Service;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;
use Repository\DistrictRepository;
use Validator\DistrictValidator;
use Validator\NewDistrictValidator;

use Repository\CityRepository;

class DistrictService
{
    private $districtRepository;

    private $districtValidator;

    private $newDistrictValidator;

    private $cityRepository;

    public function __construct(
        DistrictRepository $districtRepository,
        DistrictValidator $districtValidator,
        NewDistrictValidator $newDistrictValidator,
        CityRepository $cityRepository
    ) {
        $this->districtRepository = $districtRepository;
        $this->districtValidator = $districtValidator;
        $this->newDistrictValidator = $newDistrictValidator;
        $this->cityRepository = $cityRepository;
    }

    public function get(string $id): District
    {
        $district = $this->districtRepository->get(intval($id));
        if (!$district) {
            throw new NotFoundException();
        }
        return $district;
    }

    public function add(string $name, string $area, string $population, string $cityId): void
    {
        $cityId = intval($cityId);
        $name = trim($name);
        $area = floatval($area);
        $population = intval($population);
        $validationResult = $this->newDistrictValidator->validate($cityId, $name, $area, $population);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = new District(
            $name,
            $area,
            $population,
        );
        $district->setCity($this->cityRepository->get($cityId));
        $this->districtRepository->add($district);
    }

    public function update(string $id, string $name, string $area, string $population): void
    {
        $district = $this->get($id);
        $name = trim($name);
        $area = floatval($area);
        $population = intval($population);
        $validationResult = $this->districtValidator->validate($name, $area, $population);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district->setName($name);
        $district->setArea($area);
        $district->setPopulation($population);
        $this->districtRepository->update($district);
    }

    public function remove(string $id): void
    {
        $this->districtRepository->remove($this->get($id));
    }

    public function list(DistrictOrdering $order, ?DistrictFilter $filter): array
    {
        return $this->districtRepository->list($order, $filter);
    }
}
