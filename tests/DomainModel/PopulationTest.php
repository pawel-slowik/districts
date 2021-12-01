<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel;

use Districts\DomainModel\Exception\InvalidPopulationException;
use Districts\DomainModel\Population;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Population
 */
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
