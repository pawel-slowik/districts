<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\CityNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\Filter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CityNameFilter::class)]
#[CoversClass(Filter::class)]
final class CityNameFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = new DomainCityNameFilter("hola");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("c.name LIKE :search", $filter->where);
    }

    public function testParameters(): void
    {
        $domainFilter = new DomainCityNameFilter("foo");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame(["search" => "%foo%"], $filter->parameters);
    }

    public function testEscapeLike(): void
    {
        $domainFilter = new DomainCityNameFilter("%");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("%\\%%", $filter->parameters["search"]);
    }
}
