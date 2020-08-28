<?php

declare(strict_types=1);

namespace Test\UI\Web\ErrorHandler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

use UI\Web\ErrorHandler\HttpMethodNotAllowedHandler;

/**
 * @covers \UI\Web\ErrorHandler\HttpMethodNotAllowedHandler
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