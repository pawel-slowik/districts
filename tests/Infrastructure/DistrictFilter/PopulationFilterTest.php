<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use Districts\Infrastructure\DistrictFilter\PopulationFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\PopulationFilter
 */
class PopulationFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = $this->createStub(DomainPopulationFilter::class);

        $filter = new PopulationFilter($domainFilter);

        $this->assertSame("d.population.population >= :low AND d.population.population <= :high", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = $this->createStub(DomainPopulationFilter::class);
        $domainFilter->method("getBegin")->willReturn(3);
        $domainFilter->method("getEnd")->willReturn(4);

        $filter = new PopulationFilter($domainFilter);

        $this->assertSame(["low" => 3, "high" => 4], $filter->parameters());
    }
}
