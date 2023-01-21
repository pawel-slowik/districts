<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\CityNameFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\CityNameFilter
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\Filter
 */
class CityNameFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = new DomainCityNameFilter("hola");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("c.name LIKE :search", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = new DomainCityNameFilter("foo");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame(["search" => "%foo%"], $filter->parameters());
    }

    public function testEscapeLike(): void
    {
        $domainFilter = new DomainCityNameFilter("%");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("%\\%%", $filter->parameters()["search"]);
    }
}
