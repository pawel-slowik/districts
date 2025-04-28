<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\Infrastructure;

use Districts\Core\Domain\District;
use Districts\Editor\Domain\DistrictFilter\AreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictFilter\NameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Districts\Test\Integration\DoctrineDbTestCase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(DoctrineDistrictRepository::class)]
final class DoctrineDistrictRepositoryListTest extends DoctrineDbTestCase
{
    private DoctrineDistrictRepository $districtRepository;

    private DistrictOrdering $defaultOrder;

    private Pagination $pagination;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFiles(
            [
                "tests/Integration/Editor/data/cities.sql",
                "tests/Integration/Editor/data/districts.sql",
            ]
        );
        $this->districtRepository = new DoctrineDistrictRepository(
            $this->entityManager,
            new FilterFactory(),
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc);
        $this->pagination = new Pagination(1, 99_999);
    }

    public function testListStructure(): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, $this->pagination);
        $this->assertCount(15, $result->currentPageEntries);
        $this->assertContainsOnlyInstancesOf(District::class, $result->currentPageEntries);
    }

    /**
     * @param string[] $expectedCityNames
     */
    #[DataProvider('listOrderCityDataProvider')]
    public function testListOrderCity(DistrictOrdering $order, array $expectedCityNames): void
    {
        $result = $this->districtRepository->listWithPagination($order, $this->pagination);
        $actualCityNames = array_values(array_unique(array_map(
            static fn ($district) => $district->getCity()->getName(),
            $result->currentPageEntries,
        )));
        $this->assertSame($expectedCityNames, $actualCityNames);
    }

    /**
     * @return array<array{0: DistrictOrdering, 1: string[]}>
     */
    public static function listOrderCityDataProvider(): array
    {
        return [
            [
                new DistrictOrdering(DistrictOrderingField::CityName, OrderingDirection::Asc),
                ["Bar", "Foo"],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::CityName, OrderingDirection::Desc),
                ["Foo", "Bar"],
            ],
        ];
    }

    /**
     * @param int[] $expectedIds
     */
    #[DataProvider('listOrderDataProvider')]
    public function testListOrder(DistrictOrdering $order, array $expectedIds): void
    {
        $result = $this->districtRepository->listWithPagination($order, $this->pagination);
        $actualIds = array_map(
            static fn ($district) => $district->getId(),
            $result->currentPageEntries,
        );
        $this->assertSame($expectedIds, $actualIds);
    }

    /**
     * @return array<array{0: DistrictOrdering, 1: int[]}>
     */
    public static function listOrderDataProvider(): array
    {
        return [
            [
                new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc),
                [14, 12, 15, 13, 4, 6, 2, 9, 1, 10, 3, 5, 8, 7, 11],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Desc),
                [11, 7, 8, 5, 3, 10, 1, 9, 2, 6, 4, 13, 15, 12, 14],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::DistrictName, OrderingDirection::Asc),
                [4, 14, 6, 2, 9, 1, 10, 3, 5, 8, 7, 12, 15, 13, 11],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::DistrictName, OrderingDirection::Desc),
                [11, 13, 15, 12, 7, 8, 5, 3, 10, 1, 9, 2, 6, 14, 4],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::Area, OrderingDirection::Asc),
                [3, 4, 6, 7, 1, 2, 8, 11, 12, 13, 14, 15, 5, 10, 9],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::Area, OrderingDirection::Desc),
                [9, 10, 5, 15, 14, 13, 12, 11, 2, 8, 1, 7, 6, 4, 3],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::Population, OrderingDirection::Asc),
                [10, 2, 3, 5, 6, 4, 11, 8, 9, 1, 12, 7, 13, 14, 15],
            ],
            [
                new DistrictOrdering(DistrictOrderingField::Population, OrderingDirection::Desc),
                [15, 14, 13, 7, 12, 1, 8, 9, 11, 4, 6, 2, 3, 5, 10],
            ],
        ];
    }

    /**
     * @param int[] $expectedIds
     */
    #[DataProvider('listFilterDataProvider')]
    public function testListFilter(?Filter $filter, array $expectedIds): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, $this->pagination, $filter);
        $actualIds = array_map(
            static fn ($district) => $district->getId(),
            $result->currentPageEntries,
        );
        sort($expectedIds);
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    /**
     * @return array<array{0: ?Filter, 1: int[]}>
     */
    public static function listFilterDataProvider(): array
    {
        return [
            [
                null,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                new CityNameFilter("Bar"),
                [12, 13, 14, 15],
            ],
            [
                new CityNameFilter("o"),
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                new NameFilter("Xyzzy"),
                [11],
            ],
            [
                new NameFilter("bb"),
                [12, 13, 15],
            ],
            [
                new AreaFilter(100, 101),
                [5, 10],
            ],
            [
                new PopulationFilter(900, 1300),
                [2, 3, 5, 6, 10],
            ],
        ];
    }

    public function testCurrentPageEntryCount(): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertCount(10, $result->currentPageEntries);
    }

    public function testTotalRecordsCount(): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertSame(15, $result->totalEntryCount);
    }

    public function testPageCount(): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertSame(2, $result->pageCount);
    }

    public function testCountPageOutsideOfRange(): void
    {
        $result = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(999, 10));
        $this->assertEmpty($result->currentPageEntries);
    }
}
