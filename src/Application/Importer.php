<?php

declare(strict_types=1);

namespace Districts\Application;

use Districts\DomainModel\Entity\City;
use Districts\Repository\CityRepository;

class Importer
{
    private $cityRepository;

    public function __construct(
        CityRepository $cityRepository
    ) {
        $this->cityRepository = $cityRepository;
    }

    public function import(
        string $cityName,
        iterable $districtDTOs,
        ?\Districts\Service\ProgressReporter $progressReporter = null
    ): void {
        $city = $this->cityRepository->findByName($cityName);
        if ($city) {
            $city->removeAllDistricts();
            $this->cityRepository->update($city);
        } else {
            $city = new City($cityName);
            $this->cityRepository->add($city);
        }
        foreach ($districtDTOs as $districtDTO) {
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
