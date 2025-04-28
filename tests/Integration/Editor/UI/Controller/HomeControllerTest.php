<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\Editor\UI\Controller\HomeController;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[CoversClass(HomeController::class)]
#[RunTestsInSeparateProcesses]
final class HomeControllerTest extends BaseTestCase
{
    public function testRedirect(): void
    {
        $response = $this->runApp("GET", "/");
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/list", $response->getHeader("location")[0]);
    }
}
