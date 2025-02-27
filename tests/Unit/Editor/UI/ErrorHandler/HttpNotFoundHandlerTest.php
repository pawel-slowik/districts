<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\ErrorHandler;

use Districts\Editor\UI\ErrorHandler\HttpNotFoundHandler;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(HttpNotFoundHandler::class)]
class HttpNotFoundHandlerTest extends BaseTestCase
{
    public function testResponse(): void
    {
        $response = (new HttpNotFoundHandler())($this->requestMock, $this->exceptionMock, false, false, false);
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("not found", (string) $response->getBody());
    }
}
