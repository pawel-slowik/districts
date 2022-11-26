<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Domain\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use Districts\Infrastructure\DistrictFilter\AreaFilter as DqlAreaFilter;
use Districts\Infrastructure\DistrictFilter\CityNameFilter as DqlCityNameFilter;
use Districts\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Infrastructure\DistrictFilter\NameFilter as DqlNameFilter;
use Districts\Infrastructure\DistrictFilter\NullFilter as DqlNullFilter;
use Districts\Infrastructure\DistrictFilter\PopulationFilter as DqlPopulationFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\FilterFactory
 */
class FilterFactoryTest extends TestCase
{
    private FilterFactory $filterFactory;

    protected function setUp(): void
    {
        $this->filterFactory = new FilterFactory();
    }

    /**
     * @dataProvider typesDataProvider
     */
    public function testTypes(?DomainFilter $domainFilter, string $expectedDqlFilterClass): void
    {
        $dqlFilter = $this->filterFactory->fromDomainFilter($domainFilter);

        $this->assertInstanceOf($expectedDqlFilterClass, $dqlFilter);
    }

    public function typesDataProvider(): array
    {
        return [
            [null, DqlNullFilter::class],
            [$this->createStub(DomainAreaFilter::class), DqlAreaFilter::class],
            [$this->createStub(DomainCityNameFilter::class), DqlCityNameFilter::class],
            [$this->createStub(DomainNameFilter::class), DqlNameFilter::class],
            [$this->createStub(DomainPopulationFilter::class), DqlPopulationFilter::class],
        ];
    }

    public function testExceptionOnUnknownDomainFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->filterFactory->fromDomainFilter($this->createStub(DomainFilter::class));
    }
}
