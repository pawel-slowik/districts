<?php

declare(strict_types=1);

namespace Service;

use DomainModel\Entity\City;
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
        $validator = new NewDistrictValidator($this->districtValidator, new CityIterator($this->cityRepository));
        $validationResult = $validator->validate($cityId, $name, $area, $population);
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

    public function setDistrictsForCityName(
        string $cityName,
        iterable $districts,
        ?ProgressReporter $progressReporter = null
    ): void {
        $city = $this->cityRepository->findByName($cityName);
        if ($city) {
            $this->districtRepository->removeMultiple($city->listDistricts());
        } else {
            $city = new City($cityName);
            $this->cityRepository->add($city);
        }
        foreach ($this->prepareDistricts($districts, $city) as $district) {
            $this->districtRepository->add($district);
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
    }

    public function remove(string $id): void
    {
        $this->districtRepository->remove($this->get($id));
    }

    public function listDistricts(DistrictOrdering $order, ?DistrictFilter $filter): array
    {
        return $this->districtRepository->list($order, $filter);
    }

    public function listCities(): array
    {
        return $this->cityRepository->list();
    }

    private function prepareDistricts(iterable $districts, City $city): iterable
    {
        foreach ($districts as $district) {
            $city->addDistrict($district);
            $district->setCity($city);
            yield $district;
        }
    }
}
