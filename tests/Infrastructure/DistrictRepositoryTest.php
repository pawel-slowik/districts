<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\DomainModel\Entity\District;
use Districts\DomainModel\DistrictOrdering;
use Districts\Infrastructure\CityRepository;
use Districts\Infrastructure\DistrictRepository;
use Districts\Infrastructure\NotFoundInRepositoryException;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictRepository
 */
class DistrictRepositoryTest extends TestCase
{
    /**
     * @var DistrictRepository
     */
    private $districtRepository;

    /**
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var DistrictOrdering
     */
    private $defaultOrder;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Infrastructure/data/cities.sql",
            "tests/Infrastructure/data/districts.sql",
        ]);
        $this->districtRepository = new DistrictRepository($entityManager);
        $this->cityRepository = new CityRepository($entityManager);
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testGet(): void
    {
        $district = $this->districtRepository->get(1);
        $this->assertInstanceOf(District::class, $district);
        $this->assertSame("Plugh", $district->getName());
        $this->assertSame(floatval(10), $district->getArea());
        $this->assertSame(5000, $district->getPopulation());
        $this->assertSame("Foo", $district->getCity()->getName());
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(NotFoundInRepositoryException::class);
        $district = $this->districtRepository->get(999);
    }
}
