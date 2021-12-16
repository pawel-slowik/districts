<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\View\OrderingUrlGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Routing\RoutingResults;

/**
 * @covers \Districts\UI\Web\View\OrderingUrlGenerator
 */
class OrderingUrlGeneratorTest extends TestCase
{
    private OrderingUrlGenerator $orderingUrlGenerator;

    /**
     * @var RouteParserInterface|Stub
     */
    private $routeParser;

    protected function setUp(): void
    {
        $this->routeParser = $this->createStub(RouteParserInterface::class);
        $this->routeParser
            ->method("urlFor")
            ->willReturnCallback(
                // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
                function (string $routeName, array $routeArgs, array $queryParams): string {
                    $url = "/list/{$routeArgs['column']}/{$routeArgs['direction']}";
                    if (count($queryParams) > 0) {
                        $queryString = http_build_query($queryParams);
                        $url .= ("?" . $queryString);
                    }
                    return $url;
                }
            );

        $this->orderingUrlGenerator = new OrderingUrlGenerator($this->routeParser);
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testSimple(string $routeName, string $column, array $routeArgs, array $queryParams): void
    {
        $request = $this->createRequestMockWithAttributes($this->createRequestAttributes($routeName));
        $url = $this->orderingUrlGenerator->createOrderingUrl($request, $column, $routeArgs, $queryParams);

        $this->assertSame("/list/column1/asc", $url);
    }

    public function simpleDataProvider(): array
    {
        return [
            ["foo", "column1", [], []],
            ["foo", "column1", ["column" => "column1"], []],
            ["foo", "column1", ["direction" => "asc"], []],
            ["foo", "column1", ["column" => "column1", "direction" => "desc"], []],
        ];
    }

    public function testReversesDirection(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            $this->createRequestMockWithAttributes($this->createRequestAttributes("foo")),
            "column1",
            ["column" => "column1", "direction" => "asc"],
            []
        );

        $this->assertSame("/list/column1/desc", $url);
    }

    public function testCopiesRelevantQueryParams(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            $this->createRequestMockWithAttributes($this->createRequestAttributes("foo")),
            "column1",
            [],
            ["filterColumn" => "bar", "filterValue" => "baz"]
        );

        $this->assertStringContainsString("filterColumn=bar", $url);
        $this->assertStringContainsString("filterValue=baz", $url);
    }

    public function testSkipsIrreleventQueryParams(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            $this->createRequestMockWithAttributes($this->createRequestAttributes("foo")),
            "column1",
            [],
            ["qux" => "bar"]
        );

        $this->assertStringNotContainsString("qux", $url);
    }

    public function testExceptionOnUnroutedRequest(): void
    {
        $unroutedRequest = $this->createRequestMockWithAttributes(
            array_merge(
                $this->createRequestAttributes("test"),
                [RouteContext::ROUTE => null],
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->orderingUrlGenerator->createOrderingUrl($unroutedRequest, "column", [], []);
    }

    public function testExceptionOnUnnamedRoute(): void
    {
        $unnamedRouteRequest = $this->createRequestMockWithAttributes($this->createRequestAttributes(null));
        $this->expectException(InvalidArgumentException::class);
        $this->orderingUrlGenerator->createOrderingUrl($unnamedRouteRequest, "column", [], []);
    }

    private function createRequestAttributes(?string $routeName): array
    {
        $route = $this->createMock(RouteInterface::class);
        $route->method("getName")->willReturn($routeName);
        $route->method("getArguments")->willReturn([]);
        return [
            RouteContext::ROUTE => $route,
            RouteContext::ROUTE_PARSER => $this->createMock(RouteParserInterface::class),
            RouteContext::ROUTING_RESULTS => $this->createMock(RoutingResults::class),
            RouteContext::BASE_PATH => null,
        ];
    }

    private function createRequestMockWithAttributes(array $attributes): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method("getAttribute")->will(
            $this->returnValueMap(
                $this->convertRequestAttributesToMockArgMap($attributes),
            )
        );
        $request->method("getQueryParams")->willReturn([]);
        $request->method("getUri")->willReturn($this->createMock(UriInterface::class));
        return $request;
    }

    private function convertRequestAttributesToMockArgMap(array $attributes): array
    {
        $map = [];
        foreach ($attributes as $name => $value) {
            $map[] = [$name, null, $value];
        }
        return $map;
    }
}
