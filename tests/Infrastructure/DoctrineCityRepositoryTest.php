<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\Entity\City;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\DoctrineDistrictRepository;
use Districts\Infrastructure\NotFoundInRepositoryException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DoctrineCityRepository
 */
class DoctrineCityRepositoryTest extends TestCase
{
    /**
     * @var DoctrineCityRepository
     */
    private $cityRepository;

    /**
     * @var DoctrineDistrictRepository
     */
    private $districtRepository;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Infrastructure/data/cities.sql",
            "tests/Infrastructure/data/districts.sql",
        ]);
        $this->cityRepository = new DoctrineCityRepository($entityManager);
        $this->districtRepository = new DoctrineDistrictRepository($entityManager);
    }

    public function testGet(): void
    {
        $city = $this->cityRepository->get(1);
        $this->assertSame("Foo", $city->getName());
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(NotFoundInRepositoryException::class);
        $city = $this->cityRepository->get(999);
    }

    public function testGetByDistrictId(): void
    {
        $city = $this->cityRepository->getByDistrictId(12);
        $this->assertSame(2, $city->getId());
    }

    public function testByNonexistendDistrictId(): void
    {
        $this->expectException(NotFoundInRepositoryException::class);
        $city = $this->cityRepository->getByDistrictId(999);
    }

    public function testFind(): void
    {
        $city = $this->cityRepository->findByName("Foo");
        $this->assertNotNull($city);
        $this->assertSame(1, $city->getId());
    }

    public function testFindUnicode(): void
    {
        $city = $this->cityRepository->findByName(
            "Za\xc5\xbc\xc3\xb3\xc5\x82\xc4\x87 g\xc4\x99\xc5\x9bl\xc4\x85 ja\xc5\xba\xc5\x84"
        );
        $this->assertNotNull($city);
        $this->assertSame(3, $city->getId());
    }

    public function testFindNonExistent(): void
    {
        $city = $this->cityRepository->findByName("example non existent city name");
        $this->assertNull($city);
    }

    public function testListStructure(): void
    {
        $list = $this->cityRepository->list();
        $this->assertCount(3, $list);
        $this->assertContainsOnlyInstancesOf(City::class, $list);
    }

    public function testListContents(): void
    {
        $list = $this->cityRepository->list();
        usort(
            $list,
            function ($a, $b) {
                return $a->getId() - $b->getId();
            }
        );
        $this->assertSame(1, $list[0]->getId());
        $this->assertSame("Foo", $list[0]->getName());
        $this->assertSame(2, $list[1]->getId());
        $this->assertSame("Bar", $list[1]->getName());
        $this->assertSame(3, $list[2]->getId());
        $this->assertSame(
            "Za\xc5\xbc\xc3\xb3\xc5\x82\xc4\x87 g\xc4\x99\xc5\x9bl\xc4\x85 ja\xc5\xba\xc5\x84",
            $list[2]->getName()
        );
    }

    public function testAdd(): void
    {
        $countBefore = count($this->cityRepository->list());
        $this->cityRepository->add(new City("Baz"));
        $this->assertCount($countBefore + 1, $this->cityRepository->list());
        $this->assertNotNull($this->cityRepository->findByName("Baz"));
    }

    public function testUpdateWithNewDistrict(): void
    {
        $defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
        $newDistrictFilter = new DistrictFilter(DistrictFilter::TYPE_NAME, "New District");
        $city = $this->cityRepository->get(1);
        $city->addDistrict("New District", 123.4, 5678);
        $this->cityRepository->update($city);

        $allDistricts = $this->districtRepository->list($defaultOrder);
        $this->assertCount(16, $allDistricts);

        $newDistricts = $this->districtRepository->list($defaultOrder, $newDistrictFilter);
        $this->assertCount(1, $newDistricts);
        $this->assertSame(123.4, $newDistricts[0]->getArea());
        $this->assertSame(5678, $newDistricts[0]->getPopulation());
    }

    public function testUpdateWithChangedDistrict(): void
    {
        $defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
        $updatedDistrictFilter = new DistrictFilter(DistrictFilter::TYPE_NAME, "Updated District");
        $city = $this->cityRepository->get(1);
        $city->updateDistrict(1, "Updated District", 123.4, 5678);
        $this->cityRepository->update($city);

        $allDistricts = $this->districtRepository->list($defaultOrder);
        $this->assertCount(15, $allDistricts);

        $updatedDistricts = $this->districtRepository->list($defaultOrder, $updatedDistrictFilter);
        $this->assertCount(1, $updatedDistricts);
        $this->assertSame(123.4, $updatedDistricts[0]->getArea());
        $this->assertSame(5678, $updatedDistricts[0]->getPopulation());
    }
}
