<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\ErrorHandler;

use Districts\Editor\UI\ErrorHandler\HttpMethodNotAllowedHandler;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HttpMethodNotAllowedHandler::class)]
class HttpMethodNotAllowedHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpMethodNotAllowedHandler())($this->requestMock, $this->exceptionMock, false, false, false);
        $this->assertSame(StatusCode::STATUS_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("method not allowed", (string) $response->getBody());
    }
}
