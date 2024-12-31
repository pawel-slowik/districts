<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\CityNameFilter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CityNameFilter::class)]
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
