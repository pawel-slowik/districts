<?php

declare(strict_types=1);

namespace Service;

use DomainModel\Entity\City;
use DomainModel\Entity\District;
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

    public function import(
        string $cityName,
        iterable $districtDTOs,
        ?ProgressReporter $progressReporter = null
    ): void {
        $city = $this->cityRepository->findByName($cityName);
        if ($city) {
            $this->districtRepository->removeMultiple($city->listDistricts());
        } else {
            $city = new City($cityName);
            $this->cityRepository->add($city);
        }
        foreach ($this->prepareDistricts($districtDTOs, $city) as $district) {
            $this->districtRepository->add($district);
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
    }

    private function prepareDistricts(iterable $districtDTOs, City $city): iterable
    {
        foreach ($districtDTOs as $districtDTO) {
            $district = new District(
                $districtDTO->getName(),
                $districtDTO->getArea(),
                $districtDTO->getPopulation(),
            );
            $city->addDistrict($district);
            $district->setCity($city);
            yield $district;
        }
    }
}
