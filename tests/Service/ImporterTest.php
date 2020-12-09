<?php

declare(strict_types=1);

namespace Districts\Test\Service;

use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;

use Districts\Service\DistrictService;
use Districts\Service\Importer;
use Districts\Service\ValidationException;
use Districts\Validator\DistrictValidator;
use Districts\Scraper\District\DistrictDTO;
use Districts\Repository\DistrictRepository;
use Districts\Repository\CityRepository;

use Districts\Test\Repository\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Service\Importer
 */
class ImporterTest extends TestCase
{
    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var DistrictRepository
     */
    private $districtRepository;

    /**
     * @var DistrictOrdering
     */
    private $defaultOrder;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);
        $this->districtRepository = new DistrictRepository($entityManager);
        $cityRepository = new CityRepository($entityManager);
        $districtService = new DistrictService(
            $this->districtRepository,
            new DistrictValidator(),
            $cityRepository,
        );
        $this->importer = new Importer(
            $districtService,
            $this->districtRepository,
            $cityRepository,
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testSetDistrictsForCityName(): void
    {
        $this->importer->import("Bar", [new DistrictDTO("Hola", 1, 2)]);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertCount(1, $list);
    }

    public function testSetEmptyDistrictsForCityName(): void
    {
        $this->importer->import("Bar", []);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertEmpty($list);
    }

    public function testSetDistrictsForNonexistentCityName(): void
    {
        $this->importer->import("New City", [new DistrictDTO("Hola", 1, 2)]);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "New City"),
        );
        $this->assertCount(1, $list);
    }

    public function testExceptionOnInvalidName(): void
    {
        $this->expectException(ValidationException::class);
        $this->importer->import("Bar", [new DistrictDTO("", 1, 2)]);
    }

    public function testExceptionOnInvalidArea(): void
    {
        $this->expectException(ValidationException::class);
        $this->importer->import("Bar", [new DistrictDTO("Hola", 0, 2)]);
    }

    public function testExceptionOnInvalidPopulation(): void
    {
        $this->expectException(ValidationException::class);
        $this->importer->import("Bar", [new DistrictDTO("Hola", 1, 0)]);
    }
}
