<?php

declare(strict_types=1);

namespace Test\Controller;

/**
 * @covers \Controller\AddFormController
 * @runTestsInSeparateProcesses
 */
class AddFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/add");
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }
}
