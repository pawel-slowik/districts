<?php

declare(strict_types=1);

namespace Test\Repository;

use Entity\District;
use Repository\CityRepository;
use Repository\DistrictRepository;
use Repository\ProgressReporter;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Repository\DistrictRepository
 */
class DistrictRepositoryTest extends TestCase
{
    private $districtRepository;

    private $cityRepository;

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
        $newDistrict = new District("Lorem ipsum", 12.3, 456);
        $newDistrict->setCity($this->cityRepository->findByName("Foo"));
        $this->districtRepository->add($newDistrict);
        $this->assertCount(
            16,
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT
            )
        );
        $this->assertNotEmpty(
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT,
                DistrictRepository::FILTER_NAME,
                "Lorem ipsum"
            )
        );
    }

    /**
     * @dataProvider addMultipleDataProvider
     */
    public function testAddMultiple(
        array $newDistricts,
        int $expectedCount,
        array $expectedNames
    ): void {
        foreach ($newDistricts as $newDistrict) {
            $newDistrict->setCity($this->cityRepository->findByName("Foo"));
        }
        $this->districtRepository->addMultiple($newDistricts);
        $this->assertCount(
            $expectedCount,
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT
            )
        );
        foreach ($expectedNames as $expectedName) {
            $this->assertNotEmpty(
                $this->districtRepository->list(
                    DistrictRepository::ORDER_DEFAULT,
                    DistrictRepository::FILTER_NAME,
                    $expectedName
                )
            );
        }
    }

    /**
     * @dataProvider addMultipleDataProvider
     */
    public function testAddMultipleWithProgressReporting(
        array $newDistricts,
        int $expectedCount,
        array $expectedNames
    ): void {
        foreach ($newDistricts as $newDistrict) {
            $newDistrict->setCity($this->cityRepository->findByName("Foo"));
        }
        $progressReporter = $this->createMock(ProgressReporter::class);
        $progressReporter->expects($this->exactly(count($newDistricts)))->method("advance");
        $this->districtRepository->addMultiple($newDistricts, $progressReporter);
        $this->assertCount(
            $expectedCount,
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT
            )
        );
        foreach ($expectedNames as $expectedName) {
            $this->assertNotEmpty(
                $this->districtRepository->list(
                    DistrictRepository::ORDER_DEFAULT,
                    DistrictRepository::FILTER_NAME,
                    $expectedName
                )
            );
        }
    }

    public function addMultipleDataProvider(): array
    {
        return [
            [
                [
                    new District("Lorem ipsum", 12.3, 456),
                    new District("nulla pariatur", 7.8, 901),
                ],
                17,
                [
                    "Lorem ipsum",
                    "nulla pariatur",
                ],
            ],
        ];
    }

    public function testUpdate(): void
    {
        $district = $this->districtRepository->get(1);
        $district->setName("update test");
        $district->setArea(111.22);
        $district->setPopulation(333);
        $district->setCity($this->cityRepository->get(2));
        $this->districtRepository->update($district);
        $updatedDistrict = $this->districtRepository->get(1);
        $this->assertSame("update test", $updatedDistrict->getName());
        $this->assertSame(111.22, $updatedDistrict->getArea());
        $this->assertSame(333, $updatedDistrict->getPopulation());
        $this->assertSame(2, $updatedDistrict->getCity()->getId());
    }

    public function testRemove(): void
    {
        $district = $this->districtRepository->get(1);
        $this->districtRepository->remove($district);
        $this->assertCount(
            14,
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT
            )
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
            $this->districtRepository->list(
                DistrictRepository::ORDER_DEFAULT
            )
        );
        $this->assertNull($this->districtRepository->get(1));
        $this->assertNull($this->districtRepository->get(10));
    }
}
