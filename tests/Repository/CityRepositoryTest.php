<?php

declare(strict_types=1);

namespace Test\Repository;

use Entity\City;
use Repository\CityRepository;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Repository\CityRepository
 */
class CityRepositoryTest extends TestCase
{
    protected $cityRepository;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, ["tests/Repository/data/cities.sql"]);
        $this->cityRepository = new CityRepository($entityManager);
    }

    public function testGet(): void
    {
        $city = $this->cityRepository->get(1);
        $this->assertInstanceOf(City::class, $city);
        $this->assertSame("Foo", $city->getName());
    }

    public function testGetNonExistent(): void
    {
        $city = $this->cityRepository->get(999);
        $this->assertNull($city);
    }

    public function testFind(): void
    {
        $city = $this->cityRepository->findByName("Foo");
        $this->assertInstanceOf(City::class, $city);
        $this->assertSame(1, $city->getId());
    }

    public function testFindUnicode(): void
    {
        $city = $this->cityRepository->findByName(
            "Za\xc5\xbc\xc3\xb3\xc5\x82\xc4\x87 g\xc4\x99\xc5\x9bl\xc4\x85 ja\xc5\xba\xc5\x84"
        );
        $this->assertInstanceOf(City::class, $city);
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
}
