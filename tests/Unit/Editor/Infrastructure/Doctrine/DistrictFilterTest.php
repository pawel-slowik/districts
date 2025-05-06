<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Infrastructure\Doctrine;

use Districts\Editor\Domain\DistrictFilter\AreaFilter;
use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use Districts\Editor\Domain\DistrictFilter\Filter as DomainFilter;
use Districts\Editor\Domain\DistrictFilter\NameFilter;
use Districts\Editor\Domain\DistrictFilter\PopulationFilter;
use Districts\Editor\Infrastructure\Doctrine\DistrictFilter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictFilter::class)]
final class DistrictFilterTest extends TestCase
{
    /**
     * @param array<string, scalar> $expectedParameters
     */
    #[DataProvider('createdParametersDataProvider')]
    public function testCreatedProperties(DomainFilter $domainFilter, array $expectedParameters): void
    {
        $filter = DistrictFilter::fromDomainFilter($domainFilter);

        $this->assertSame($expectedParameters, $filter->parameters);
    }

    /**
     * @return array<array{0: DomainFilter, 1: array<string, scalar>}>
     */
    public static function createdParametersDataProvider(): array
    {
        return [
            [
                new AreaFilter(1.1, 2.2),
                [
                    "low" => 1.1,
                    "high" => 2.2,
                ],
            ],
            [
                new CityNameFilter("hola"),
                [
                    "search" => "%hola%",
                ],
            ],
            [
                new CityNameFilter("%"),
                [
                    "search" => "%\\%%",
                ],
            ],
            [
                new NameFilter("foo"),
                [
                    "search" => "%foo%",
                ],
            ],
            [
                new NameFilter("%"),
                [
                    "search" => "%\\%%",
                ],
            ],
            [
                new PopulationFilter(3, 4),
                [
                    "low" => 3,
                    "high" => 4,
                ],
            ],
        ];
    }

    #[DataProvider('createdWhereDataProvider')]
    public function testCreatedWhere(DomainFilter $domainFilter, string $expectedWhere): void
    {
        $filter = DistrictFilter::fromDomainFilter($domainFilter);

        $this->assertSame($expectedWhere, $filter->where);
    }

    /**
     * @return array<array{0: DomainFilter, 1: string}>
     */
    public static function createdWhereDataProvider(): array
    {
        return [
            [
                new AreaFilter(1, 2),
                "d.area.area >= :low AND d.area.area <= :high",
            ],
            [
                new CityNameFilter("foo"),
                "c.name LIKE :search",
            ],
            [
                new NameFilter("bar"),
                "d.name.name LIKE :search",
            ],
            [
                new PopulationFilter(3, 4),
                "d.population.population >= :low AND d.population.population <= :high",
            ],
        ];
    }

    public function testExceptionOnUnknownDomainFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DistrictFilter::fromDomainFilter($this->createStub(DomainFilter::class));
    }
}
