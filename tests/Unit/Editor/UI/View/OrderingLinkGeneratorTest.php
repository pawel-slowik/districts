<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\UI\View\OrderingLinkGenerator;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(OrderingLinkGenerator::class)]
class OrderingLinkGeneratorTest extends TestCase
{
    private OrderingLinkGenerator $orderingLinkGenerator;

    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->uriFactory = $this->createStub(UriFactoryInterface::class);
        $this->uriFactory->method("createUri")->willReturn(new Uri());
        $this->orderingLinkGenerator = new OrderingLinkGenerator($this->uriFactory);
    }

    /**
     * @param array<string, string> $queryParams
     */
    #[DataProvider('simpleDataProvider')]
    public function testSimple(string $column, array $queryParams): void
    {
        $request = $this->createRequestStubForList($queryParams);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, $column);

        $this->assertSame("/list?orderColumn=column1&orderDirection=asc", (string) $link->url);
    }

    /**
     * @return array<array{0: string, 1: array<string, string>}>
     */
    public static function simpleDataProvider(): array
    {
        return [
            ["column1", []],
            ["column1", ["orderColumn" => "column1"]],
            ["column1", ["orderDirection" => "asc"]],
            ["column1", ["orderColumn" => "column1", "orderDirection" => "desc"]],
        ];
    }

    public function testReversesDirection(): void
    {
        $request = $this->createRequestStubForList(["orderColumn" => "column1", "orderDirection" => "asc"]);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column1");

        $this->assertSame("/list?orderColumn=column1&orderDirection=desc", (string) $link->url);
    }

    public function testSetsAscendingOrder(): void
    {
        $request = $this->createRequestStubForList(["orderColumn" => "column1", "orderDirection" => "asc"]);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column1");

        $this->assertTrue($link->isOrderedAscending);
    }

    public function testSetsDescendingOrder(): void
    {
        $request = $this->createRequestStubForList(["orderColumn" => "column1", "orderDirection" => "desc"]);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column1");

        $this->assertTrue($link->isOrderedDescending);
    }

    /**
     * @param array<string, string> $queryParams
     */
    #[DataProvider('unorderedDataProvider')]
    public function testDoesNotSetFlags(array $queryParams): void
    {
        $request = $this->createRequestStubForList($queryParams);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column_foo");

        $this->assertFalse($link->isOrderedAscending);
        $this->assertFalse($link->isOrderedDescending);
    }

    /**
     * @return array<array{0: array<string, string>}>
     */
    public static function unorderedDataProvider(): array
    {
        return [
            [[]],
            [["orderColumn" => "column_bar", "orderDirection" => "asc"]],
            [["orderColumn" => "column_bar", "orderDirection" => "desc"]],
        ];
    }

    public function testCopiesRelevantQueryParams(): void
    {
        $request = $this->createRequestStubForList(["filterColumn" => "bar", "filterValue" => "baz"]);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column1");

        $this->assertStringContainsString("filterColumn=bar", (string) $link->url);
        $this->assertStringContainsString("filterValue=baz", (string) $link->url);
    }

    public function testSkipsIrreleventQueryParams(): void
    {
        $request = $this->createRequestStubForList(["qux" => "bar"]);

        $link = $this->orderingLinkGenerator->createOrderingLink($request, "column1");

        $this->assertStringNotContainsString("qux", (string) $link->url);
    }

    /**
     * @param array<string, string> $queryParams
     */
    private function createRequestStubForList(array $queryParams): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $uri->method("getPath")->willReturn("/list");
        $request->method("getUri")->willReturn($uri);
        $request->method("getQueryParams")->willReturn($queryParams);
        return $request;
    }
}
