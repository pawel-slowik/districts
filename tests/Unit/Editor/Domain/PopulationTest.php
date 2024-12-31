<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\Exception\InvalidPopulationException;
use Districts\Editor\Domain\Population;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Population::class)]
class PopulationTest extends TestCase
{
    public function testConstructionFailsOnInvalidValue(): void
    {
        $this->expectException(InvalidPopulationException::class);

        new Population(0);
    }

    public function testEquality(): void
    {
        $this->assertTrue((new Population(123000))->equals(new Population(123000)));
        $this->assertFalse((new Population(123000))->equals(new Population(123001)));
    }

    public function testStringValue(): void
    {
        $this->assertSame("4567", (string) new Population(4567));
    }
}
