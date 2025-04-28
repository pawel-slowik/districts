<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain\DistrictFilter;

use Districts\Editor\Domain\DistrictFilter\NameFilter;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NameFilter::class)]
final class NameFilterTest extends TestCase
{
    public function testProperties(): void
    {
        $filter = new NameFilter("test");
        $this->assertSame("test", $filter->name);
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NameFilter("");
    }
}
