<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\Application\Command\AddDistrictCommand;
use Districts\DomainModel\Entity\City;
use Districts\Repository\CityRepository;

class Importer
{
    private $districtService;

    private $cityRepository;

    public function __construct(
        DistrictService $districtService,
        CityRepository $cityRepository
    ) {
        $this->districtService = $districtService;
        $this->cityRepository = $cityRepository;
    }

    public function import(
        string $cityName,
        iterable $districtDTOs,
        ?ProgressReporter $progressReporter = null
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
            $command = new AddDistrictCommand(
                $city->getId(),
                $districtDTO->getName(),
                $districtDTO->getArea(),
                $districtDTO->getPopulation(),
            );
            $this->districtService->add($command);
            if ($progressReporter) {
                $progressReporter->advance();
            }
        }
    }
}
