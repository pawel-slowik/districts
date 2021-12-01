<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\DistrictFilter;

use Districts\DomainModel\DistrictFilter\CityNameFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\DistrictFilter\CityNameFilter
 */
class CityNameFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new CityNameFilter("test");
        $this->assertSame("test", $filter->getCityName());
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CityNameFilter("");
    }
}
