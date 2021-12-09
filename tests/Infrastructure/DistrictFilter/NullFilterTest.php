<?php

declare(strict_types=1);

namespace Districts\Test\Infrastructure\DistrictFilter;

use Districts\Infrastructure\DistrictFilter\NullFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Infrastructure\DistrictFilter\NullFilter
 */
class NullFilterTest extends TestCase
{
    public function testWhere(): void
    {
        $filter = new NullFilter();

        $this->assertSame("", $filter->where());
    }

    public function testParameters(): void
    {
        $filter = new NullFilter();

        $this->assertSame([], $filter->parameters());
    }
}
