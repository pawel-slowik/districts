<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\AreaFilter as DomainAreaFilter;
use Districts\Editor\Infrastructure\DistrictFilter\AreaFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AreaFilter::class)]
final class AreaFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = new DomainAreaFilter(1, 2);

        $filter = new AreaFilter($domainFilter);

        $this->assertSame("d.area.area >= :low AND d.area.area <= :high", $filter->where);
    }

    public function testParameters(): void
    {
        $domainFilter = new DomainAreaFilter(1.1, 2.2);

        $filter = new AreaFilter($domainFilter);

        $this->assertSame(["low" => 1.1, "high" => 2.2], $filter->parameters);
    }
}
