<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\NameFilter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Domain\DistrictFilter\NameFilter
 */
class NameFilterTest extends TestCase
{
    public function testGetters(): void
    {
        $filter = new NameFilter("test");
        $this->assertSame("test", $filter->getName());
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NameFilter("");
    }
}
