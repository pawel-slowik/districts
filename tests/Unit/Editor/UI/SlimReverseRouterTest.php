<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI;

use Districts\Editor\UI\SlimReverseRouter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \Districts\Editor\UI\SlimReverseRouter
 */
class SlimReverseRouterTest extends TestCase
{
    private SlimReverseRouter $slimReverseRouter;

    /** @var MockObject&RouteParserInterface */
    private RouteParserInterface $routeParser;

    protected function setUp(): void
    {
        $this->routeParser = $this->createMock(RouteParserInterface::class);
        $this->slimReverseRouter = new SlimReverseRouter($this->routeParser);
    }

    public function testUrlFromRoute(): void
    {
        $this->routeParser
            ->method("fullUrlFor")
            ->willReturn("foo");

        $url = $this->slimReverseRouter->urlFromRoute($this->createMock(UriInterface::class), "", []);

        $this->assertSame("foo", $url);
    }
}
