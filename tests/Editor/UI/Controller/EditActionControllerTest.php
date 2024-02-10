<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Districts\Editor\UI\Controller\EditActionController
 *
 * @runTestsInSeparateProcesses
 */
class EditActionControllerTest extends BaseTestCase
{
    public function testAction(): void
    {
        $postData = [
            "name" => "test",
            "area" => "123.45",
            "population" => "6789",
        ];
        $response = $this->runApp("POST", "/edit/1", $postData);
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/list", $response->getHeader("location")[0]);
    }

    public function testInvalid(): void
    {
        $postData = [
            "name" => "",
            "area" => "",
            "population" => "",
        ];
        $response = $this->runApp("POST", "/edit/1", $postData);
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/edit/1", $response->getHeader("location")[0]);
    }

    public function testNonexistent(): void
    {
        $postData = [
            "name" => "test",
            "area" => "123.45",
            "population" => "6789",
        ];
        $response = $this->runApp("POST", "/edit/999", $postData);
        $this->assertSame(StatusCode::STATUS_NOT_FOUND, $response->getStatusCode());
    }
}
