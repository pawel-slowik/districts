<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Editor\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Editor\Domain\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use Districts\Editor\Infrastructure\DistrictFilter\AreaFilter as DqlAreaFilter;
use Districts\Editor\Infrastructure\DistrictFilter\CityNameFilter as DqlCityNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
use Districts\Editor\Infrastructure\DistrictFilter\NameFilter as DqlNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\NullFilter as DqlNullFilter;
use Districts\Editor\Infrastructure\DistrictFilter\PopulationFilter as DqlPopulationFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\FilterFactory
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
