<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\Area;
use Districts\DomainModel\City;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\Name;
use Districts\DomainModel\Population;
use Districts\DomainModel\Scraper\CityDTO;

class Importer
{
    private CityRepository $cityRepository;

    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    public function import(
        CityDTO $cityDTO,
        ?ProgressReporter $progressReporter = null
    ): void {
        $city = $this->cityRepository->findByName($cityDTO->getName());
        if ($city) {
            $city->removeAllDistricts();
            $this->cityRepository->update($city);
        } else {
            $city = new City($cityDTO->getName());
            $this->cityRepository->add($city);
        }
        foreach ($cityDTO->listDistricts() as $districtDTO) {
            $city->addDistrict(
                new Name($districtDTO->getName()),
                new Area($districtDTO->getArea()),
                new Population($districtDTO->getPopulation()),
            );
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
        $this->cityRepository->update($city);
    }
}
