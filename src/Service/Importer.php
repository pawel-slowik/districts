<?php

declare(strict_types=1);

namespace Service;

use DomainModel\Entity\City;
use Repository\DistrictRepository;

use Repository\CityRepository;

class Importer
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

    private function prepareDistricts(iterable $districts, City $city): iterable
    {
        foreach ($districts as $district) {
            $city->addDistrict($district);
            $district->setCity($city);
            yield $district;
        }
    }
}
