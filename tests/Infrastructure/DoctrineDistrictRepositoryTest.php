<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\DomainModel\DistrictOrdering;
use Districts\Infrastructure\DoctrineDistrictRepository;
use Districts\Infrastructure\NotFoundInRepositoryException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DoctrineDistrictRepository
 */
class DoctrineDistrictRepositoryTest extends TestCase
{
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
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testGet(): void
    {
        $district = $this->districtRepository->get(1);
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
