<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Domain\DistrictFilter\CityNameFilter
 */
class CityNameFilterTest extends TestCase
{
    public function testProperties(): void
    {
        $filter = new CityNameFilter("test");
        $this->assertSame("test", $filter->cityName);
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CityNameFilter("");
    }
}
