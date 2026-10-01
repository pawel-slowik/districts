<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\Editor\UI\Controller\EditFormController;
use Districts\Editor\UI\RoutingConfiguration;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[CoversClass(EditFormController::class)]
#[CoversClass(RoutingConfiguration::class)]
#[RunTestsInSeparateProcesses]
final class EditFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/edit/1");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }
}
