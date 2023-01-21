<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\PopulationFilter as DomainPopulationFilter;
use Districts\Editor\Infrastructure\DistrictFilter\PopulationFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Infrastructure\DistrictFilter\PopulationFilter
 */
class PopulationFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $domainFilter = new DomainPopulationFilter(1, 2);

        $filter = new PopulationFilter($domainFilter);

        $this->assertSame("d.population.population >= :low AND d.population.population <= :high", $filter->where());
    }

    public function testParameters(): void
    {
        $domainFilter = new DomainPopulationFilter(3, 4);

        $filter = new PopulationFilter($domainFilter);

        $this->assertSame(["low" => 3, "high" => 4], $filter->parameters());
    }
}
