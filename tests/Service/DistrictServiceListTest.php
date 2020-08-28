<?php

declare(strict_types=1);

namespace Test\Service;

use DomainModel\Entity\District;

use Service\DistrictService;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;

use Repository\CityRepository;
use Repository\DistrictRepository;

use Test\Repository\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\DistrictService
 */
class DistrictServiceListTest extends TestCase
{
    private $districtService;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);
        $this->districtService = new DistrictService(
            new DistrictRepository($entityManager),
            new CityRepository($entityManager)
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testListStructure(): void
    {
        $list = $this->districtService->listDistricts(
            $this->defaultOrder,
            null,
        );
        $this->assertCount(15, $list);
        $this->assertContainsOnlyInstancesOf(District::class, $list);
    }

    /**
     * @dataProvider listOrderCityDataProvider
     */
    public function testListOrderCity(DistrictOrdering $order, array $expectedCityNames): void
    {
        $this->assertSame(
            $expectedCityNames,
            array_values(array_unique(array_map(
                function ($district) {
                    return $district->getCity()->getName();
                },
                $this->districtService->listDistricts($order, null)
            )))
        );
    }

    public function listOrderCityDataProvider(): array
    {
        return [
            [
                new DistrictOrdering(DistrictOrdering::CITY_NAME, DistrictOrdering::ASC),
                ["Bar", "Foo"],
            ],
            [
                new DistrictOrdering(DistrictOrdering::CITY_NAME, DistrictOrdering::DESC),
                ["Foo", "Bar"],
            ],
        ];
    }

    /**
     * @dataProvider listOrderDataProvider
     */
    public function testListOrder(DistrictOrdering $order, array $expectedIds): void
    {
        $this->assertSame(
            $expectedIds,
            array_map(
                function ($district) {
                    return $district->getId();
                },
                $this->districtService->listDistricts($order, null)
            )
        );
    }

    public function listOrderDataProvider(): array
    {
        return [
            [
                new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC),
                [14, 12, 15, 13, 4, 6, 2, 9, 1, 10, 3, 5, 8, 7, 11],
            ],
            [
                new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::DESC),
                [11, 7, 8, 5, 3, 10, 1, 9, 2, 6, 4, 13, 15, 12, 14],
            ],
            [
                new DistrictOrdering(DistrictOrdering::DISTRICT_NAME, DistrictOrdering::ASC),
                [4, 14, 6, 2, 9, 1, 10, 3, 5, 8, 7, 12, 15, 13, 11],
            ],
            [
                new DistrictOrdering(DistrictOrdering::DISTRICT_NAME, DistrictOrdering::DESC),
                [11, 13, 15, 12, 7, 8, 5, 3, 10, 1, 9, 2, 6, 14, 4],
            ],
            [
                new DistrictOrdering(DistrictOrdering::AREA, DistrictOrdering::ASC),
                [3, 4, 6, 7, 1, 2, 8, 11, 12, 13, 14, 15, 5, 10, 9],
            ],
            [
                new DistrictOrdering(DistrictOrdering::AREA, DistrictOrdering::DESC),
                [9, 10, 5, 15, 14, 13, 12, 11, 2, 8, 1, 7, 6, 4, 3],
            ],
            [
                new DistrictOrdering(DistrictOrdering::POPULATION, DistrictOrdering::ASC),
                [10, 2, 3, 5, 6, 4, 11, 8, 9, 1, 12, 7, 13, 14, 15],
            ],
            [
                new DistrictOrdering(DistrictOrdering::POPULATION, DistrictOrdering::DESC),
                [15, 14, 13, 7, 12, 1, 8, 9, 11, 4, 6, 2, 3, 5, 10],
            ],
        ];
    }

    /**
     * @dataProvider listFilterDataProvider
     */
    public function testListFilter(?DistrictFilter $filter, array $expectedIds): void
    {
        sort($expectedIds);
        $actualIds = array_map(
            function ($district) {
                return $district->getId();
            },
            $this->districtService->listDistricts($this->defaultOrder, $filter)
        );
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function listFilterDataProvider(): array
    {
        return [
            [
                null,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
                [12, 13, 14, 15],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_CITY, "o"),
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_NAME, "Xyzzy"),
                [11],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_NAME, "bb"),
                [12, 13, 15],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_AREA, [100, 101]),
                [5, 10],
            ],
            [
                new DistrictFilter(DistrictFilter::TYPE_POPULATION, [900, 1300]),
                [2, 3, 5, 6, 10],
            ],
        ];
    }
}
