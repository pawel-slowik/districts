<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\Exception\InvalidNameException;
use Districts\Editor\Domain\Name;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Name::class)]
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
