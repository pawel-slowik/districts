<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use Districts\Infrastructure\DoctrineDistrictRepository;
use Districts\Infrastructure\NotFoundInRepositoryException;

/**
 * @covers \Districts\Infrastructure\DoctrineDistrictRepository
 */
class DoctrineDistrictRepositoryTest extends DoctrineDbTestCase
{
    private DoctrineDistrictRepository $districtRepository;

    protected function setUp(): void
    {
        parent::setUp();
        parent::loadDefaultDbContents();
        $this->districtRepository = new DoctrineDistrictRepository($this->entityManager);
    }

    public function testGet(): void
    {
        $district = $this->districtRepository->get(1);
        $this->assertObjectEquals(new Name("Plugh"), $district->getName());
        $this->assertObjectEquals(new Area(10), $district->getArea());
        $this->assertObjectEquals(new Population(5000), $district->getPopulation());
        $this->assertSame("Foo", $district->getCity()->getName());
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(NotFoundInRepositoryException::class);
        $district = $this->districtRepository->get(999);
    }
}
