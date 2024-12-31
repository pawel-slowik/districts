<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\Editor\UI\Controller\AddFormController;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[CoversClass(AddFormController::class)]
#[RunTestsInSeparateProcesses]
class AddFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/add");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }
}
