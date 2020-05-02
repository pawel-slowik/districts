<?php

declare(strict_types=1);

namespace Test\Controller;

/**
 * @covers \Controller\HomeController
 * @runTestsInSeparateProcesses
 */
class HomeControllerTest extends BaseTestCase
{
    public function testRedirect(): void
    {
        $response = $this->runApp("GET", "/");
        $this->assertSame(302, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/list", $response->getHeader("location")[0]);
    }

    public function testPostNotAllowed(): void
    {
        $response = $this->runApp("POST", "/");
        $this->assertSame(405, $response->getStatusCode());
    }
}
