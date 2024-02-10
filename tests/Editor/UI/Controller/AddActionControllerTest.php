<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Districts\Editor\UI\Controller\AddActionController
 *
 * @runTestsInSeparateProcesses
 */
class AddActionControllerTest extends BaseTestCase
{
    public function testAction(): void
    {
        $postData = [
            "name" => "test",
            "area" => "123.45",
            "population" => "6789",
            "city" => "3",
        ];
        $response = $this->runApp("POST", "/add", $postData);
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
            "city" => "",
        ];
        $response = $this->runApp("POST", "/add", $postData);
        $this->assertSame(StatusCode::STATUS_FOUND, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
        $this->assertTrue($response->hasHeader("location"));
        $this->assertStringEndsWith("/add", $response->getHeader("location")[0]);
    }
}
