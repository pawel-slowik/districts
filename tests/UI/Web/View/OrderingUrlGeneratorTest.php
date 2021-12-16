<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\View;

use Districts\UI\Web\View\OrderingUrlGenerator;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

/**
 * @covers \Districts\UI\Web\View\OrderingUrlGenerator
 */
class OrderingUrlGeneratorTest extends TestCase
{
    use NamedRequestTester;

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
        $request = $this->createRequestMockWithAttributes(
            $this->createRequestAttributes($routeName),
            $queryParams
        );
        $url = $this->orderingUrlGenerator->createOrderingUrl($request, $column, $routeArgs);

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
            ["column" => "column1", "direction" => "asc"]
        );

        $this->assertSame("/list/column1/desc", $url);
    }

    public function testCopiesRelevantQueryParams(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            $this->createRequestMockWithAttributes(
                $this->createRequestAttributes("foo"),
                ["filterColumn" => "bar", "filterValue" => "baz"]
            ),
            "column1",
            []
        );

        $this->assertStringContainsString("filterColumn=bar", $url);
        $this->assertStringContainsString("filterValue=baz", $url);
    }

    public function testSkipsIrreleventQueryParams(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            $this->createRequestMockWithAttributes(
                $this->createRequestAttributes("foo"),
                ["qux" => "bar"]
            ),
            "column1",
            []
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
}
