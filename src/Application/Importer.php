<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\Domain\Area;
use Districts\Domain\City;
use Districts\Domain\CityRepository;
use Districts\Domain\Name;
use Districts\Domain\Population;
use Districts\Domain\Scraper\CityDTO;

class Importer
{
    public function __construct(
        private CityRepository $cityRepository,
    ) {
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
