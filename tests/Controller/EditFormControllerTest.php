<?php

declare(strict_types=1);

namespace Test\Controller;

/**
 * @covers \Controller\EditFormController
 * @runTestsInSeparateProcesses
 */
class EditFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/edit/1");
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }

    public function testNonexistent(): void
    {
        $response = $this->runApp("GET", "/edit/999");
        $this->assertSame(404, $response->getStatusCode());
    }
}
