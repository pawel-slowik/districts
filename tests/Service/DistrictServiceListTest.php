<?php

declare(strict_types=1);

namespace Districts\Test\Service;

use Districts\DomainModel\Entity\District;

use Districts\Application\Query\ListDistrictsQuery;
use Districts\Application\DistrictService;
use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;

use Districts\Repository\CityRepository;
use Districts\Repository\DistrictRepository;

use Districts\Test\Repository\FixtureTool;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\DistrictService
 */
class DistrictServiceListTest extends TestCase
{
    /**
     * @var DistrictService
     */
    private $districtService;

    protected function setUp(): void
    {
        $entityManager = (require "doctrine-bootstrap.php")();
        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);
        $cityRepository = new CityRepository($entityManager);
        $this->districtService = new DistrictService(
            new DistrictRepository($entityManager),
            $cityRepository
        );
    }

    public function testListStructure(): void
    {
        $defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
        $list = $this->districtService->list(new ListDistrictsQuery($defaultOrder, null));
        $this->assertCount(15, $list);
        $this->assertContainsOnlyInstancesOf(District::class, $list);
    }

    /**
     * @dataProvider listOrderCityDataProvider
     */
    public function testListOrderCity(ListDistrictsQuery $query, array $expectedCityNames): void
    {
        $this->assertSame(
            $expectedCityNames,
            array_values(array_unique(array_map(
                function ($district) {
                    return $district->getCity()->getName();
                },
                $this->districtService->list($query)
            )))
        );
    }

    public function listOrderCityDataProvider(): array
    {
        return [
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::CITY_NAME, DistrictOrdering::ASC),
                    null,
                ),
                ["Bar", "Foo"],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::CITY_NAME, DistrictOrdering::DESC),
                    null,
                ),
                ["Foo", "Bar"],
            ],
        ];
    }

    /**
     * @dataProvider listOrderDataProvider
     */
    public function testListOrder(ListDistrictsQuery $query, array $expectedIds): void
    {
        $this->assertSame(
            $expectedIds,
            array_map(
                function ($district) {
                    return $district->getId();
                },
                $this->districtService->list($query)
            )
        );
    }

    public function listOrderDataProvider(): array
    {
        return [
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC),
                    null,
                ),
                [14, 12, 15, 13, 4, 6, 2, 9, 1, 10, 3, 5, 8, 7, 11],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::DESC),
                    null,
                ),
                [11, 7, 8, 5, 3, 10, 1, 9, 2, 6, 4, 13, 15, 12, 14],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::DISTRICT_NAME, DistrictOrdering::ASC),
                    null,
                ),
                [4, 14, 6, 2, 9, 1, 10, 3, 5, 8, 7, 12, 15, 13, 11],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::DISTRICT_NAME, DistrictOrdering::DESC),
                    null,
                ),
                [11, 13, 15, 12, 7, 8, 5, 3, 10, 1, 9, 2, 6, 14, 4],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::AREA, DistrictOrdering::ASC),
                    null,
                ),
                [3, 4, 6, 7, 1, 2, 8, 11, 12, 13, 14, 15, 5, 10, 9],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::AREA, DistrictOrdering::DESC),
                    null,
                ),
                [9, 10, 5, 15, 14, 13, 12, 11, 2, 8, 1, 7, 6, 4, 3],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::POPULATION, DistrictOrdering::ASC),
                    null,
                ),
                [10, 2, 3, 5, 6, 4, 11, 8, 9, 1, 12, 7, 13, 14, 15],
            ],
            [
                new ListDistrictsQuery(
                    new DistrictOrdering(DistrictOrdering::POPULATION, DistrictOrdering::DESC),
                    null,
                ),
                [15, 14, 13, 7, 12, 1, 8, 9, 11, 4, 6, 2, 3, 5, 10],
            ],
        ];
    }

    /**
     * @dataProvider listFilterDataProvider
     */
    public function testListFilter(ListDistrictsQuery $query, array $expectedIds): void
    {
        sort($expectedIds);
        $actualIds = array_map(
            function ($district) {
                return $district->getId();
            },
            $this->districtService->list($query)
        );
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function listFilterDataProvider(): array
    {
        $defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
        return [
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    null,
                ),
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_CITY, "Bar"),
                ),
                [12, 13, 14, 15],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_CITY, "o"),
                ),
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_NAME, "Xyzzy"),
                ),
                [11],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_NAME, "bb"),
                ),
                [12, 13, 15],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_AREA, [100, 101]),
                ),
                [5, 10],
            ],
            [
                new ListDistrictsQuery(
                    $defaultOrder,
                    new DistrictFilter(DistrictFilter::TYPE_POPULATION, [900, 1300]),
                ),
                [2, 3, 5, 6, 10],
            ],
        ];
    }
}
