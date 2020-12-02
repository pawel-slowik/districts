<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\ErrorHandler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

use Districts\UI\Web\ErrorHandler\HttpMethodNotAllowedHandler;

/**
 * @covers \Districts\UI\Web\ErrorHandler\HttpMethodNotAllowedHandler
 */
class HttpMethodNotAllowedHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpMethodNotAllowedHandler())($this->requestMock, $this->exceptionMock, false, false, false);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(StatusCode::STATUS_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("method not allowed", (string) $response->getBody());
    }
}
