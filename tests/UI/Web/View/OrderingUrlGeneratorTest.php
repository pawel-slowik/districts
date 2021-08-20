<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\View\OrderingUrlGenerator;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\RouteParserInterface;

/**
 * @covers \Districts\UI\Web\View\OrderingUrlGenerator
 */
class OrderingUrlGeneratorTest extends TestCase
{
    /**
     * @var OrderingUrlGenerator
     */
    private $orderingUrlGenerator;

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
    public function testSimple(...$args): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(...$args);

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
            "foo",
            "column1",
            ["column" => "column1", "direction" => "asc"],
            []
        );

        $this->assertSame("/list/column1/desc", $url);
    }

    public function testCopiesRelevantQueryParams(): void
    {
        $url = $this->orderingUrlGenerator->createOrderingUrl(
            "foo",
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
            "foo",
            "column1",
            [],
            ["qux" => "bar"]
        );

        $this->assertStringNotContainsString("qux", $url);
    }
}
