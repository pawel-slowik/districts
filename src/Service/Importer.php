<?php

declare(strict_types=1);

namespace Districts\Service;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\Validator\DistrictValidator;
use Districts\Repository\DistrictRepository;
use Districts\Repository\CityRepository;

class Importer
{
    private $districtValidator;

    private $districtRepository;

    private $cityRepository;

    public function __construct(
        DistrictValidator $districtValidator,
        DistrictRepository $districtRepository,
        CityRepository $cityRepository
    ) {
        $this->districtValidator = $districtValidator;
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
            $validationResult = $this->districtValidator->validate(
                $city->getId(),
                $districtDTO->getName(),
                $districtDTO->getArea(),
                $districtDTO->getPopulation(),
            );
            if (!$validationResult->isOk()) {
                throw (new ValidationException())->withErrors($validationResult->getErrors());
            }
            $district = new District(
                $city,
                $districtDTO->getName(),
                $districtDTO->getArea(),
                $districtDTO->getPopulation(),
            );
            yield $district;
        }
    }
}
