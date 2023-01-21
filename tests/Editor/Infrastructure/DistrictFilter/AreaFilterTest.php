<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Editor\Infrastructure\DistrictFilter\AreaFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\AreaFilter
 */
class AreaFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = $this->createStub(DomainAreaFilter::class);

        $filter = new AreaFilter($domainFilter);

        $this->assertSame("d.area.area >= :low AND d.area.area <= :high", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = $this->createStub(DomainAreaFilter::class);
        $domainFilter->method("getBegin")->willReturn(1.1);
        $domainFilter->method("getEnd")->willReturn(2.2);

        $filter = new AreaFilter($domainFilter);

        $this->assertSame(["low" => 1.1, "high" => 2.2], $filter->parameters());
    }
}
