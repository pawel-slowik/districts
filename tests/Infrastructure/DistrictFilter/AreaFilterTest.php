<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Infrastructure\DistrictFilter\AreaFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\AreaFilter
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
