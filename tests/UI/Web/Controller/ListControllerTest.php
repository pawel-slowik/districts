<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Controller;

use DI\Container;
use Districts\Test\Infrastructure\FixtureTool;
use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

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

    /**
     * @dataProvider listDataProvider
     */
    public function testListWithPaging(string $url): void
    {
        $response = $this->runAppWithPaginatedDataset("GET", $url);
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

    public function testFilteringError(): void
    {
        $response = $this->runApp("GET", "/list?filterColumn=population&filterValue=2-1");
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("invalid query parameters", (string) $response->getBody());
    }

    protected function runAppWithPaginatedDataset(
        string $requestMethod,
        string $requestUri,
        array $requestData = []
    ): ResponseInterface {
        $container = new Container();
        $app = $this->createApp($container);
        /** @var EntityManager */
        $entityManager = $container->get(EntityManager::class);
        FixtureTool::reset($entityManager);
        FixtureTool::loadFiles($entityManager, [
            "tests/Infrastructure/data/cities_and_districts_for_pagination_tests.sql",
        ]);
        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }
}
