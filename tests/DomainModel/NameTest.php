<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel;

use Districts\DomainModel\Exception\InvalidNameException;
use Districts\DomainModel\Name;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Name
 */
class NameTest extends TestCase
{
    public function testConstructionFailsOnInvalidValue(): void
    {
        $this->expectException(InvalidNameException::class);

        new Name("");
    }

    public function testEquality(): void
    {
        $this->assertTrue((new Name("foo"))->equals(new Name("foo")));
        $this->assertFalse((new Name("foo"))->equals(new Name("Foo")));
        $this->assertFalse((new Name("foo"))->equals(new Name("foo ")));
    }

    public function testStringValue(): void
    {
        $this->assertSame("bar", (string) new Name("bar"));
    }
}
