<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI;

use Districts\Editor\UI\Session;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\UI\Session
 */
class SessionTest extends TestCase
{
    private Session $session;

    protected function setUp(): void
    {
        $this->session = new Session();
    }

    public function testGetAndDelete(): void
    {
        $this->session->set("foo", "bar");
        $value = $this->session->getAndDelete("foo");
        $existsAfter = $this->session->exists("foo");

        $this->assertSame("bar", $value);
        $this->assertFalse($existsAfter);
    }
}
