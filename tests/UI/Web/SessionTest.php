<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\Session;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Session
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
