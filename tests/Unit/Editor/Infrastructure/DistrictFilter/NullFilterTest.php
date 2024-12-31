<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Infrastructure\DistrictFilter;

use Districts\Editor\Infrastructure\DistrictFilter\NullFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullFilter::class)]
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
