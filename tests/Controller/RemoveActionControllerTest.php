<?php

declare(strict_types=1);

namespace Test\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Controller\RemoveActionController
 * @runTestsInSeparateProcesses
 */
class RemoveActionControllerTest extends BaseTestCase
{
    public function testAction(): void
    {
        $response = $this->runApp("POST", "/remove/1", ["confirm" => ""]);
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/list", $response->getHeader("location")[0]);
    }

    public function testNonexistent(): void
    {
        $response = $this->runApp("POST", "/remove/999", ["confirm" => ""]);
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
