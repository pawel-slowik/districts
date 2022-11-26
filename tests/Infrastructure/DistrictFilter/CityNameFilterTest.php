<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\CityNameFilter as DomainCityNameFilter;
use Districts\Infrastructure\DistrictFilter\CityNameFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\CityNameFilter
 * @covers \Districts\Infrastructure\DistrictFilter\Filter
 */
class CityNameFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = $this->createStub(DomainCityNameFilter::class);

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("c.name LIKE :search", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = $this->createStub(DomainCityNameFilter::class);
        $domainFilter->method("getCityName")->willReturn("foo");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame(["search" => "%foo%"], $filter->parameters());
    }

    public function testEscapeLike(): void
    {
        $domainFilter = $this->createStub(DomainCityNameFilter::class);
        $domainFilter->method("getCityName")->willReturn("%");

        $filter = new CityNameFilter($domainFilter);

        $this->assertSame("%\\%%", $filter->parameters()["search"]);
    }
}
