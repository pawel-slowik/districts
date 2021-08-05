<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Importer;
use Districts\Application\ProgressReporter;
use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Exception\InvalidAreaException;
use Districts\DomainModel\Exception\InvalidNameException;
use Districts\DomainModel\Exception\InvalidPopulationException;
use Districts\DomainModel\Scraper\CityDTO;
use Districts\DomainModel\Scraper\DistrictDTO;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\DoctrineDistrictRepository;
use Districts\Test\Infrastructure\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\Importer
 */
class ImporterTest extends TestCase
{
    /**
     * @var Importer
     */
    private $importer;

    /**
     * @var DoctrineDistrictRepository
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
        FixtureTool::loadFiles($entityManager, [
            "tests/Infrastructure/data/cities.sql",
            "tests/Infrastructure/data/districts.sql",
        ]);
        $this->districtRepository = new DoctrineDistrictRepository($entityManager);
        $this->importer = new Importer(new DoctrineCityRepository($entityManager));
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testSetDistrictsForCityName(): void
    {
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("Hola", 1, 2)]));
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertCount(1, $list);
    }

    public function testSetEmptyDistrictsForCityName(): void
    {
        $this->importer->import(new CityDTO("Bar", []));
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
        );
        $this->assertEmpty($list);
    }

    public function testSetDistrictsForNonexistentCityName(): void
    {
        $this->importer->import(new CityDTO("New City", [new DistrictDTO("Hola", 1, 2)]));
        $list = $this->districtRepository->list(
            $this->defaultOrder,
            new DistrictFilter(DistrictFilter::TYPE_CITY, "New City"),
        );
        $this->assertCount(1, $list);
    }

    public function testProgressReport(): void
    {
        $progressReporter = $this->createMock(ProgressReporter::class);
        $progressReporter
            ->expects($this->exactly(2))
            ->method("advance");

        $this->importer->import(
            new CityDTO(
                "Foo",
                [
                    new DistrictDTO("Bar", 1, 2),
                    new DistrictDTO("Baz", 3, 4),
                ]
            ),
            $progressReporter
        );
    }

    public function testExceptionOnInvalidName(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("", 1, 2)]));
    }

    public function testExceptionOnInvalidArea(): void
    {
        $this->expectException(InvalidAreaException::class);
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("Hola", 0, 2)]));
    }

    public function testExceptionOnInvalidPopulation(): void
    {
        $this->expectException(InvalidPopulationException::class);
        $this->importer->import(new CityDTO("Bar", [new DistrictDTO("Hola", 1, 0)]));
    }
}
