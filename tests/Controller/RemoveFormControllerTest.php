<?php

declare(strict_types=1);

namespace Test\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Controller\RemoveFormController
 * @runTestsInSeparateProcesses
 */
class RemoveFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/remove/1");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }

    public function testNonexistent(): void
    {
        $response = $this->runApp("GET", "/remove/999");
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
