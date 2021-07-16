<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\ErrorHandler;

use Districts\UI\Web\ErrorHandler\HttpNotFoundHandler;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

/**
 * @covers \Districts\UI\Web\ErrorHandler\HttpNotFoundHandler
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
