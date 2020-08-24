<?php

declare(strict_types=1);

namespace Service;

use Entity\City;
use Entity\District;
use Repository\DistrictRepository;
use Validator\DistrictValidator;
use Validator\NewDistrictValidator;

use Repository\CityRepository;

class DistrictService
{
    public const FILTER_CITY = 1;
    public const FILTER_NAME = 2;
    public const FILTER_AREA = 3;
    public const FILTER_POPULATION = 4;

    public const ORDER_DEFAULT = 0;
    public const ORDER_CITY_ASC = 1;
    public const ORDER_CITY_DESC = 2;
    public const ORDER_NAME_ASC = 3;
    public const ORDER_NAME_DESC = 4;
    public const ORDER_AREA_ASC = 5;
    public const ORDER_AREA_DESC = 6;
    public const ORDER_POPULATION_ASC = 7;
    public const ORDER_POPULATION_DESC = 8;

    private const REPOSITORY_FILTER_MAP = [
        self::FILTER_CITY => DistrictRepository::FILTER_CITY,
        self::FILTER_NAME => DistrictRepository::FILTER_NAME,
        self::FILTER_AREA => DistrictRepository::FILTER_AREA,
        self::FILTER_POPULATION => DistrictRepository::FILTER_POPULATION,
    ];

    private const REPOSITORY_ORDER_MAP = [
        self::ORDER_DEFAULT => DistrictRepository::ORDER_DEFAULT,
        self::ORDER_CITY_ASC => DistrictRepository::ORDER_CITY_ASC,
        self::ORDER_CITY_DESC => DistrictRepository::ORDER_CITY_DESC,
        self::ORDER_NAME_ASC => DistrictRepository::ORDER_NAME_ASC,
        self::ORDER_NAME_DESC => DistrictRepository::ORDER_NAME_DESC,
        self::ORDER_AREA_ASC => DistrictRepository::ORDER_AREA_ASC,
        self::ORDER_AREA_DESC => DistrictRepository::ORDER_AREA_DESC,
        self::ORDER_POPULATION_ASC => DistrictRepository::ORDER_POPULATION_ASC,
        self::ORDER_POPULATION_DESC => DistrictRepository::ORDER_POPULATION_DESC,
    ];

    private $districtRepository;

    private $cityRepository;

    public function __construct(
        DistrictRepository $districtRepository,
        CityRepository $cityRepository
    ) {
        $this->districtRepository = $districtRepository;
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
        $input = [
            "name" => trim($name),
            "area" => floatval($area),
            "population" => intval($population),
            "city" => intval($cityId),
        ];
        $validCityIds = array_map(
            function ($city) {
                return $city->getId();
            },
            $this->cityRepository->list()
        );
        $validator = new NewDistrictValidator($validCityIds);
        $validationResult = $validator->validate($input);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district = new District(
            $input["name"],
            $input["area"],
            $input["population"]
        );
        $district->setCity($this->cityRepository->get($input["city"]));
        $this->districtRepository->add($district);
    }

    public function update(string $id, string $name, string $area, string $population): void
    {
        $district = $this->get($id);
        $input = [
            "name" => trim($name),
            "area" => floatval($area),
            "population" => intval($population),
        ];
        $validator = new DistrictValidator();
        $validationResult = $validator->validate($input);
        if (!$validationResult->isOk()) {
            throw (new ValidationException())->withErrors($validationResult->getErrors());
        }
        $district->setName($input["name"]);
        $district->setArea($input["area"]);
        $district->setPopulation($input["population"]);
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

    public function listDistricts(int $orderBy, ?DistrictFilter $filter): array
    {
        return $this->districtRepository->list(
            self::REPOSITORY_ORDER_MAP[$orderBy],
            is_null($filter) ? null : self::REPOSITORY_FILTER_MAP[$filter->getType()],
            is_null($filter) ? null : $filter->getValue(),
        );
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
