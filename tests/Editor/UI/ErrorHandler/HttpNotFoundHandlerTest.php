<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\ErrorHandler;

use Districts\Editor\UI\ErrorHandler\HttpNotFoundHandler;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Districts\Editor\UI\ErrorHandler\HttpNotFoundHandler
 */
class HttpNotFoundHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpNotFoundHandler())($this->requestMock, $this->exceptionMock, false, false, false);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("not found", (string) $response->getBody());
    }
}
