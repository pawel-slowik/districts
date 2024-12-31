<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\Editor\UI\Controller\ListController;
use Districts\Test\Integration\FixtureTool;
use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Http\Message\ResponseInterface;

#[CoversClass(ListController::class)]
#[RunTestsInSeparateProcesses]
class ListControllerTest extends BaseTestCase
{
    #[DataProvider('listDataProvider')]
    public function testList(string $url): void
    {
        $response = $this->runApp("GET", $url);
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }

    #[DataProvider('listDataProvider')]
    public function testListWithPaging(string $url): void
    {
        $response = $this->runAppWithPaginatedDataset("GET", $url);
        $this->assertSame(StatusCode::STATUS_OK, $response->getStatusCode());
        $this->assertNotEmpty((string) $response->getBody());
    }

    /**
     * @return array<array{0: string}>
     */
    public static function listDataProvider(): array
    {
        return [
            ["/list"],
            ["/list?orderColumn=name&orderDirection=asc"],
            ["/list?filterColumn=name&filterValue=ow"],
            ["/list?orderColumn=name&orderDirection=asc&filterColumn=city&filterValue=gda"],
            ["/list?orderColumn=name&orderDirection=asc&filterColumn=name&filterValue=ow"],
            ["/list?orderColumn=name&orderDirection=asc&filterColumn=area&filterValue=1-100"],
            ["/list?orderColumn=name&orderDirection=asc&filterColumn=population&filterValue=1000"],
            ["/list?orderColumn=name&orderDirection=asc&filterColumn=foo&filterValue=bar"],
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

    /**
     * @param array<string, string> $requestData
     */
    protected function runAppWithPaginatedDataset(
        string $requestMethod,
        string $requestUri,
        array $requestData = []
    ): ResponseInterface {
        $container = $this->createContainer();
        $app = $this->createApp($container);
        /** @var EntityManager */
        $entityManager = $container->get(EntityManager::class);
        FixtureTool::reset($entityManager);
        FixtureTool::loadFiles($entityManager, [
            "tests/Integration/Editor/Infrastructure/data/cities_and_districts_for_pagination_tests.sql",
        ]);
        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }
}
