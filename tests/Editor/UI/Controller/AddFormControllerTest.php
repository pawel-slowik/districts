<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Districts\Editor\UI\Controller\AddFormController
 *
 * @runTestsInSeparateProcesses
 */
class AddFormControllerTest extends BaseTestCase
{
    public function testForm(): void
    {
        $response = $this->runApp("GET", "/add");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsString("<form", (string) $response->getBody());
    }
}
