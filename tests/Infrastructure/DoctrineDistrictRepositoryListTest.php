<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure;

use Districts\Domain\District;
use Districts\Domain\DistrictFilter\AreaFilter;
use Districts\Domain\DistrictFilter\CityNameFilter;
use Districts\Domain\DistrictFilter\Filter;
use Districts\Domain\DistrictFilter\NameFilter;
use Districts\Domain\DistrictFilter\PopulationFilter;
use Districts\Domain\DistrictOrdering;
use Districts\Domain\Pagination;
use Districts\Infrastructure\DistrictFilter\AreaFilter as DqlAreaFilter;
use Districts\Infrastructure\DistrictFilter\CityNameFilter as DqlCityNameFilter;
use Districts\Infrastructure\DistrictFilter\Filter as DqlFilter;
use Districts\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Infrastructure\DistrictFilter\NameFilter as DqlNameFilter;
use Districts\Infrastructure\DistrictFilter\NullFilter as DqlNullFilter;
use Districts\Infrastructure\DistrictFilter\PopulationFilter as DqlPopulationFilter;
use Districts\Infrastructure\DoctrineDistrictRepository;
use PHPUnit\Framework\MockObject\Stub;

/**
 * @covers \Districts\Infrastructure\DoctrineDistrictRepository
 */
class DoctrineDistrictRepositoryListTest extends DoctrineDbTestCase
{
    private DoctrineDistrictRepository $districtRepository;

    /** @var FilterFactory&Stub */
    private FilterFactory $filterFactory;

    private DistrictOrdering $defaultOrder;

    protected function setUp(): void
    {
        parent::setUp();
        parent::loadDefaultDbContents();
        $this->filterFactory = $this->createStub(FilterFactory::class);
        $this->districtRepository = new DoctrineDistrictRepository(
            $this->entityManager,
            $this->filterFactory
        );
        $this->defaultOrder = new DistrictOrdering(DistrictOrdering::FULL_NAME, DistrictOrdering::ASC);
    }

    public function testListStructure(): void
    {
        $list = $this->districtRepository->list($this->defaultOrder);
        $this->assertCount(15, $list->getCurrentPageEntries());
        $this->assertContainsOnlyInstancesOf(District::class, $list->getCurrentPageEntries());
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
                $this->districtRepository->list($order)->getCurrentPageEntries()
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
                $this->districtRepository->list($order)->getCurrentPageEntries()
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
    public function testListFilter(?Filter $filter, DqlFilter $dqlFilter, array $expectedIds): void
    {
        $this->filterFactory
            ->method("fromDomainFilter")
            ->with($this->identicalTo($filter))
            ->willReturn($dqlFilter);

        sort($expectedIds);
        $actualIds = array_map(
            function ($district) {
                return $district->getId();
            },
            $this->districtRepository->list($this->defaultOrder, $filter)->getCurrentPageEntries()
        );
        sort($actualIds);
        $this->assertSame($expectedIds, $actualIds);
    }

    public function listFilterDataProvider(): array
    {
        return [
            [
                null,
                new DqlNullFilter(),
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

    public function testCountSinglePage(): void
    {
        $list = $this->districtRepository->list($this->defaultOrder, null, new Pagination(1, 10));
        $this->assertCount(10, $list->getCurrentPageEntries());
    }

    public function testCountPageOutsideOfRange(): void
    {
        $list = $this->districtRepository->list($this->defaultOrder, null, new Pagination(999, 10));
        $this->assertEmpty($list->getCurrentPageEntries());
    }
}
