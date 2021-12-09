<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\DomainModel\DistrictFilter\NameFilter as DomainNameFilter;
use Districts\Infrastructure\DistrictFilter\NameFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\Filter
 * @covers \Districts\Infrastructure\DistrictFilter\NameFilter
 */
class NameFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = $this->createStub(DomainNameFilter::class);

        $filter = new NameFilter($domainFilter);

        $this->assertSame("d.name.name LIKE :search", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = $this->createStub(DomainNameFilter::class);
        $domainFilter->method("getName")->willReturn("bar");

        $filter = new NameFilter($domainFilter);

        $this->assertSame(["search" => "%bar%"], $filter->parameters());
    }

    public function testEscapeLike(): void
    {
        $domainFilter = $this->createStub(DomainNameFilter::class);
        $domainFilter->method("getName")->willReturn("%");

        $filter = new NameFilter($domainFilter);

        $this->assertSame("%\\%%", $filter->parameters()["search"]);
    }
}
