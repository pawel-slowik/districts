<?php

declare(strict_types=1);

namespace Districts\Test\Repository;

use Districts\DomainModel\Entity\District;
use Districts\DomainModel\DistrictOrdering;
use Districts\Repository\CityRepository;
use Districts\Repository\DistrictRepository;
use Districts\Repository\NotFoundException;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Repository\DistrictRepository
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
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
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
        $this->expectException(NotFoundException::class);
        $district = $this->districtRepository->get(999);
    }

    public function testRemove(): void
    {
        $district = $this->districtRepository->get(1);
        $this->districtRepository->remove($district);
        $this->assertCount(
            14,
            $this->districtRepository->list($this->defaultOrder)
        );
        $this->expectException(NotFoundException::class);
        $this->districtRepository->get(1);
    }

    public function testRemoveMultiple(): void
    {
        $this->districtRepository->removeMultiple(
            [
                $this->districtRepository->get(1),
                $this->districtRepository->get(10),
            ]
        );
        $this->assertCount(
            13,
            $this->districtRepository->list($this->defaultOrder)
        );
        $this->expectException(NotFoundException::class);
        $this->districtRepository->get(1);
        $this->expectException(NotFoundException::class);
        $this->districtRepository->get(10);
    }
}
