<?php

declare(strict_types=1);

namespace Test\ErrorHandlers;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

use ErrorHandlers\HttpMethodNotAllowedHandler;

/**
 * @covers \ErrorHandlers\HttpMethodNotAllowedHandler
 */
class HttpMethodNotAllowedHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpMethodNotAllowedHandler())($this->requestMock, $this->exceptionMock, false);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(StatusCode::STATUS_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("method not allowed", (string) $response->getBody());
    }
}
