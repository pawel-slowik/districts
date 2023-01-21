<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\NameFilter as DomainNameFilter;
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
