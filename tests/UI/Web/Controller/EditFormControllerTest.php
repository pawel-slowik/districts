<?php

declare(strict_types=1);

namespace Test\UI\Web\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \UI\Web\Controller\EditFormController
 * @runTestsInSeparateProcesses
 */
class EditFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/edit/1");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }

    public function testNonexistent(): void
    {
        $response = $this->runApp("GET", "/edit/999");
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
