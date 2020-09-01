<?php

declare(strict_types=1);

namespace Test\Repository;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;
use Repository\CityRepository;
use Repository\DistrictRepository;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Repository\DistrictRepository
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
        $district = $this->districtRepository->get(999);
        $this->assertNull($district);
    }

    public function testAdd(): void
    {
        $newDistrict = new District($this->cityRepository->findByName("Foo"), "Lorem ipsum", 12.3, 456);
        $this->districtRepository->add($newDistrict);
        $this->assertCount(
            16,
            $this->districtRepository->list(
                $this->defaultOrder,
            )
        );
        $this->assertNotEmpty(
            $this->districtRepository->list(
                $this->defaultOrder,
                new DistrictFilter(DistrictFilter::TYPE_NAME, "Lorem ipsum"),
            )
        );
    }

    public function testUpdate(): void
    {
        $district = $this->districtRepository->get(1);
        $district->setName("update test");
        $district->setArea(111.22);
        $district->setPopulation(333);
        $this->districtRepository->update($district);
        $updatedDistrict = $this->districtRepository->get(1);
        $this->assertSame("update test", $updatedDistrict->getName());
        $this->assertSame(111.22, $updatedDistrict->getArea());
        $this->assertSame(333, $updatedDistrict->getPopulation());
    }

    public function testRemove(): void
    {
        $district = $this->districtRepository->get(1);
        $this->districtRepository->remove($district);
        $this->assertCount(
            14,
            $this->districtRepository->list($this->defaultOrder)
        );
        $this->assertNull($this->districtRepository->get(1));
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
        $this->assertNull($this->districtRepository->get(1));
        $this->assertNull($this->districtRepository->get(10));
    }
}
