<?php

declare(strict_types=1);

namespace Test\Service;

use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;

use Service\Importer;
use Service\ValidationException;
use Validator\DistrictValidator;
use Scraper\District\DistrictDTO;
use Repository\DistrictRepository;
use Repository\CityRepository;

use Test\Repository\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\Importer
 */
class ImporterTest extends TestCase
{
    private $importer;

    private $districtRepository;

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
        $this->importer = new Importer(
            new DistrictValidator(),
            $this->districtRepository,
            new CityRepository($entityManager),
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
