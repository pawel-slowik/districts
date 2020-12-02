<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Districts\UI\Web\Controller\HomeController
 * @runTestsInSeparateProcesses
 */
class HomeControllerTest extends BaseTestCase
{
    public function testRedirect(): void
    {
        $response = $this->runApp("GET", "/");
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/list", $response->getHeader("location")[0]);
    }

    public function testPostNotAllowed(): void
    {
        $response = $this->runApp("POST", "/");
        $this->assertSame(StatusCode::STATUS_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }
}
