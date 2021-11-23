<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\Redirector;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \Districts\UI\Web\Redirector
 */
class RedirectorTest extends TestCase
{
    private Redirector $redirector;

    /**
     * @var MockObject|RouteParserInterface
     */
    private $routeParser;

    protected function setUp(): void
    {
        $this->routeParser = $this->createMock(RouteParserInterface::class);
        $this->redirector = new Redirector($this->routeParser);
    }

    public function testRedirect(): void
    {
        $this->routeParser
            ->method("fullUrlFor")
            ->willReturn("foo");

        $response = $this->redirector->redirect($this->createMock(UriInterface::class), "", []);

        $this->assertSame($response->getStatusCode(), StatusCode::STATUS_FOUND);
        $this->assertSame($response->getHeader("location"), ["foo"]);
    }
}
