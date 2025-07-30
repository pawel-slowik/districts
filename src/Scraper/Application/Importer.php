<?php

declare(strict_types=1);

namespace Districts\Scraper\Application;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\City;
use Districts\Core\Domain\CityRepository;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Scraper\Domain\CityDTO;

readonly class Importer
{
    public function __construct(
        private CityRepository $cityRepository,
    ) {
    }

    public function import(CityDTO $cityDTO): void
    {
        $city = $this->cityRepository->findByName($cityDTO->name);
        if ($city !== null) {
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
        }
        $this->cityRepository->update($city);
    }
}
