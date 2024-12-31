<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\Editor\UI\Controller\RemoveFormController;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[CoversClass(RemoveFormController::class)]
#[RunTestsInSeparateProcesses]
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
