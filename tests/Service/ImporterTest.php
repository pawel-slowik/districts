<?php

declare(strict_types=1);

namespace Test\Service;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;

use Service\Importer;
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

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);
        $this->districtRepository = new DistrictRepository($entityManager);
        $this->importer = new Importer($this->districtRepository, new CityRepository($entityManager));
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testSetDistrictsForCityName(): void
    {
        $this->importer->setDistrictsForCityName("Bar", [new District("Hola", 1, 2)]);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertCount(1, $list);
    }

    public function testSetEmptyDistrictsForCityName(): void
    {
        $this->importer->setDistrictsForCityName("Bar", []);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertEmpty($list);
    }

    public function testSetDistrictsForNonexistentCityName(): void
    {
        $this->importer->setDistrictsForCityName("New City", [new District("Hola", 1, 2)]);
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "New City"),
        );
        $this->assertCount(1, $list);
    }
}
