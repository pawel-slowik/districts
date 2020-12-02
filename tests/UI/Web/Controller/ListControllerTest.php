<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;

/**
 * @covers \Districts\UI\Web\Controller\ListController
 * @runTestsInSeparateProcesses
 */
class ListControllerTest extends BaseTestCase
{
    /**
     * @dataProvider listDataProvider
     */
    public function testList(string $url): void
    {
        $response = $this->runApp("GET", $url);
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }

    public function listDataProvider(): array
    {
        return [
            ["/list"],
            ["/list/order/name/asc"],
            ["/list?filterColumn=name&filterValue=ow"],
            ["/list/order/name/asc?filterColumn=city&filterValue=gda"],
            ["/list/order/name/asc?filterColumn=name&filterValue=ow"],
            ["/list/order/name/asc?filterColumn=area&filterValue=1-100"],
            ["/list/order/name/asc?filterColumn=population&filterValue=1000"],
            ["/list/order/name/asc?filterColumn=foo&filterValue=bar"],
        ];
    }

    public function testPostNotAllowed(): void
    {
        $response = $this->runApp("POST", "/list");
        $this->assertSame(StatusCode::STATUS_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }
}
