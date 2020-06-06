<?php

declare(strict_types=1);

namespace Test\ErrorHandlers;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

use ErrorHandlers\HttpNotFoundHandler;

/**
 * @covers \ErrorHandlers\HttpNotFoundHandler
 */
class HttpNotFoundHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpNotFoundHandler())($this->requestMock, $this->exceptionMock, false);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("not found", (string) $response->getBody());
    }
}
