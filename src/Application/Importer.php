<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\Entity\City;
use Districts\Infrastructure\CityRepository;
use Districts\Scraper\CityDTO;

class Importer
{
    private $cityRepository;

    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    public function import(
        CityDTO $cityDTO,
        ?\Districts\Service\ProgressReporter $progressReporter = null
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
                $districtDTO->getName(),
                $districtDTO->getArea(),
                $districtDTO->getPopulation(),
            );
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
        $this->cityRepository->update($city);
    }
}
