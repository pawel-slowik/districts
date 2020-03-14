<?php

declare(strict_types=1);

namespace Test\Service;

use Entity\District;

use Service\DistrictService;

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
    }

    public function testListStructure(): void
    {
        $list = $this->districtService->listDistricts(
            DistrictService::ORDER_DEFAULT,
            DistrictService::FILTER_NONE,
            null,
        );
        $this->assertCount(15, $list);
        $this->assertContainsOnlyInstancesOf(District::class, $list);
    }

    /**
     * @dataProvider listOrderCityDataProvider
     */
    public function testListOrderCity(int $sortType, array $expectedCityNames): void
    {
        $this->assertSame(
            $expectedCityNames,
            array_values(array_unique(array_map(
                function ($district) {
                    return $district->getCity()->getName();
                },
                $this->districtService->listDistricts($sortType, DistrictService::FILTER_NONE, null)
            )))
        );
    }

    public function listOrderCityDataProvider(): array
    {
        return [
            [
                DistrictService::ORDER_CITY_ASC,
                ["Bar", "Foo"],
            ],
            [
                DistrictService::ORDER_CITY_DESC,
                ["Foo", "Bar"],
            ],
        ];
    }

    /**
     * @dataProvider listOrderDataProvider
     */
    public function testListOrder(int $sortType, array $expectedIds): void
    {
        $this->assertSame(
            $expectedIds,
            array_map(
                function ($district) {
                    return $district->getId();
                },
                $this->districtService->listDistricts($sortType, DistrictService::FILTER_NONE, null)
            )
        );
    }

    public function listOrderDataProvider(): array
    {
        return [
            [
                DistrictService::ORDER_DEFAULT,
                [14, 12, 15, 13, 4, 6, 2, 9, 1, 10, 3, 5, 8, 7, 11],
            ],
            [
                DistrictService::ORDER_NAME_ASC,
                [4, 14, 6, 2, 9, 1, 10, 3, 5, 8, 7, 12, 15, 13, 11],
            ],
            [
                DistrictService::ORDER_NAME_DESC,
                [11, 13, 15, 12, 7, 8, 5, 3, 10, 1, 9, 2, 6, 14, 4],
            ],
            [
                DistrictService::ORDER_AREA_ASC,
                [3, 4, 6, 7, 1, 2, 8, 11, 12, 13, 14, 15, 5, 10, 9],
            ],
            [
                DistrictService::ORDER_AREA_DESC,
                [9, 10, 5, 15, 14, 13, 12, 11, 2, 8, 1, 7, 6, 4, 3],
            ],
            [
                DistrictService::ORDER_POPULATION_ASC,
                [10, 2, 3, 5, 6, 4, 11, 8, 9, 1, 12, 7, 13, 14, 15],
            ],
            [
                DistrictService::ORDER_POPULATION_DESC,
                [15, 14, 13, 7, 12, 1, 8, 9, 11, 4, 6, 2, 3, 5, 10],
            ],
        ];
    }

    /**
     * @dataProvider listFilterDataProvider
     */
    public function testListFilter(int $filterType, $filterValue, array $expectedIds): void
    {
        sort($expectedIds);
        $actualIds = array_map(
            function ($district) {
                return $district->getId();
            },
            $this->districtService->listDistricts(DistrictService::ORDER_DEFAULT, $filterType, $filterValue)
        );
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function listFilterDataProvider(): array
    {
        return [
            [
                DistrictService::FILTER_NONE,
                null,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                DistrictService::FILTER_CITY,
                "Bar",
                [12, 13, 14, 15],
            ],
            [
                DistrictService::FILTER_CITY,
                "o",
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                DistrictService::FILTER_NAME,
                "Xyzzy",
                [11],
            ],
            [
                DistrictService::FILTER_NAME,
                "bb",
                [12, 13, 15],
            ],
            [
                DistrictService::FILTER_AREA,
                [100, 101],
                [5, 10],
            ],
            [
                DistrictService::FILTER_POPULATION,
                [900, 1300],
                [2, 3, 5, 6, 10],
            ],
        ];
    }
}
