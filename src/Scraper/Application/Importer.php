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
        $city = $this->cityRepository->findByName($cityDTO->name);
        if ($city) {
            $city->removeAllDistricts();
            $this->cityRepository->update($city);
        } else {
            $city = new City($cityDTO->name);
            $this->cityRepository->add($city);
        }
        foreach ($cityDTO->districts as $districtDTO) {
            $city->addDistrict(
                new Name($districtDTO->name),
                new Area($districtDTO->area),
                new Population($districtDTO->population),
            );
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
        $this->cityRepository->update($city);
    }
}
