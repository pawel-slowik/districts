<?php

declare(strict_types=1);

namespace Test\Controller;

/**
 * @covers \Controller\RemoveFormController
 * @runTestsInSeparateProcesses
 */
class RemoveFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/remove/1");
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }

    public function testNonexistent(): void
    {
        $response = $this->runApp("GET", "/remove/999");
        $this->assertSame(404, $response->getStatusCode());
    }
}
