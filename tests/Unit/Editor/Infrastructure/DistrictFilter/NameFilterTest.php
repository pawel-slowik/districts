<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Editor\Infrastructure\DistrictFilter\NameFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\Filter
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\NameFilter
 */
class NameFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = new DomainNameFilter("foo");

        $filter = new NameFilter($domainFilter);

        $this->assertSame("d.name.name LIKE :search", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = new DomainNameFilter("bar");

        $filter = new NameFilter($domainFilter);

        $this->assertSame(["search" => "%bar%"], $filter->parameters());
    }

    public function testEscapeLike(): void
    {
        $domainFilter = new DomainNameFilter("%");

        $filter = new NameFilter($domainFilter);

        $this->assertSame("%\\%%", $filter->parameters()["search"]);
    }
}
