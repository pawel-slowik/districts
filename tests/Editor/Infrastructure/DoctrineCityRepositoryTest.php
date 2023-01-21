<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure;

use Districts\Editor\Domain\City;
use Districts\Editor\Infrastructure\DoctrineCityRepository;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;

/**
 * @covers \Districts\Editor\Infrastructure\DoctrineCityRepository
 */
class DoctrineCityRepositoryTest extends DoctrineDbTestCase
{
    private DoctrineCityRepository $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();
        parent::loadDefaultDbContents();
        $this->cityRepository = new DoctrineCityRepository($this->entityManager);
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
}
