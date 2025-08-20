<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Core\Domain;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\Exception\InvalidAreaException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Area::class)]
final class AreaTest extends TestCase
{
    public function testConstructionFailsOnInvalidValue(): void
    {
        $this->expectException(InvalidAreaException::class);

        new Area(0);
    }

    public function testEquality(): void
    {
        $this->assertTrue(new Area(1.23)->equals(new Area(1.23)));
        $this->assertFalse(new Area(1.23)->equals(new Area(1.2300001)));
    }

    public function testStringValue(): void
    {
        $this->assertSame("4.56", (string) new Area(4.56));
    }

    public function testFloatValue(): void
    {
        $this->assertSame(7.89, new Area(7.89)->asFloat());
    }
}
