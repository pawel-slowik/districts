<?php

declare(strict_types=1);

namespace Districts\Scraper\Application;

use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\Population;
use Districts\Scraper\Domain\CityDTO;

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
