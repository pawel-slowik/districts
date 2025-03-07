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
use Districts\Editor\Infrastructure\DistrictFilter\AreaFilter as DqlAreaFilter;
use Districts\Editor\Infrastructure\DistrictFilter\CityNameFilter as DqlCityNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\Filter as DqlFilter;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Editor\Infrastructure\DistrictFilter\NameFilter as DqlNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\PopulationFilter as DqlPopulationFilter;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Districts\Test\Integration\DoctrineDbTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;

#[CoversClass(DoctrineDistrictRepository::class)]
class DoctrineDistrictRepositoryListTest extends DoctrineDbTestCase
{
    private DoctrineDistrictRepository $districtRepository;

    /** @var FilterFactory&Stub */
    private FilterFactory $filterFactory;

    private DistrictOrdering $defaultOrder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFiles(
            [
                "tests/Integration/Editor/data/cities.sql",
                "tests/Integration/Editor/data/districts.sql",
            ]
        );
        $this->filterFactory = $this->createStub(FilterFactory::class);
        $this->districtRepository = new DoctrineDistrictRepository(
            $this->entityManager,
            $this->filterFactory
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc);
    }

    public function testListStructure(): void
    {
        $list = $this->districtRepository->list($this->defaultOrder);
        $this->assertCount(15, $list);
        $this->assertContainsOnlyInstancesOf(District::class, $list);
    }

    /**
     * @param string[] $expectedCityNames
     */
    #[DataProvider('listOrderCityDataProvider')]
    public function testListOrderCity(DistrictOrdering $order, array $expectedCityNames): void
    {
        $this->assertSame(
            $expectedCityNames,
            array_values(array_unique(array_map(
                static fn ($district) => $district->getCity()->getName(),
                $this->districtRepository->list($order)
            )))
        );
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
        $this->assertSame(
            $expectedIds,
            array_map(
                static fn ($district) => $district->getId(),
                $this->districtRepository->list($order)
            )
        );
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
    public function testListFilter(?Filter $filter, ?DqlFilter $dqlFilter, array $expectedIds): void
    {
        $this->filterFactory
            ->method("fromDomainFilter")
            ->willReturnMap([[$filter, $dqlFilter]]);

        sort($expectedIds);
        $actualIds = array_map(
            static fn ($district) => $district->getId(),
            $this->districtRepository->list($this->defaultOrder, $filter)
        );
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    /**
     * @return array<array{0: ?Filter, 1: ?DqlFilter, 2: int[]}>
     */
    public static function listFilterDataProvider(): array
    {
        return [
            [
                null,
                null,
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            ],
            [
                new CityNameFilter("Bar"),
                new DqlCityNameFilter(new CityNameFilter("Bar")),
                [12, 13, 14, 15],
            ],
            [
                new CityNameFilter("o"),
                new DqlCityNameFilter(new CityNameFilter("o")),
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            ],
            [
                new NameFilter("Xyzzy"),
                new DqlNameFilter(new NameFilter("Xyzzy")),
                [11],
            ],
            [
                new NameFilter("bb"),
                new DqlNameFilter(new NameFilter("bb")),
                [12, 13, 15],
            ],
            [
                new AreaFilter(100, 101),
                new DqlAreaFilter(new AreaFilter(100, 101)),
                [5, 10],
            ],
            [
                new PopulationFilter(900, 1300),
                new DqlPopulationFilter(new PopulationFilter(900, 1300)),
                [2, 3, 5, 6, 10],
            ],
        ];
    }

    public function testCurrentPageEntryCount(): void
    {
        $list = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertCount(10, $list->currentPageEntries);
    }

    public function testTotalRecordsCount(): void
    {
        $list = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertSame(15, $list->totalEntryCount);
    }

    public function testPageCount(): void
    {
        $list = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(1, 10));
        $this->assertSame(2, $list->pageCount);
    }

    public function testCountPageOutsideOfRange(): void
    {
        $list = $this->districtRepository->listWithPagination($this->defaultOrder, new Pagination(999, 10));
        $this->assertEmpty($list->currentPageEntries);
    }
}
