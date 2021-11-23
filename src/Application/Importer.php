<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\CityRepository;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;

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
