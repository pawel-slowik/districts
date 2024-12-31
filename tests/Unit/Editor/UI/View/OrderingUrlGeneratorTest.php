<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\UI\View\OrderingUrlGenerator;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(OrderingUrlGenerator::class)]
class OrderingUrlGeneratorTest extends TestCase
{
    private OrderingUrlGenerator $orderingUrlGenerator;

    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->uriFactory = $this->createStub(UriFactoryInterface::class);
        $this->uriFactory->method("createUri")->willReturn(new Uri());
        $this->orderingUrlGenerator = new OrderingUrlGenerator($this->uriFactory);
    }

    /**
     * @param array<string, string> $queryParams
     */
    #[DataProvider('simpleDataProvider')]
    public function testSimple(string $column, array $queryParams): void
    {
        $request = $this->createRequestStubForList($queryParams);

        $url = $this->orderingUrlGenerator->createOrderingUrl($request, $column);

        $this->assertSame("/list?orderColumn=column1&orderDirection=asc", $url);
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

        $url = $this->orderingUrlGenerator->createOrderingUrl($request, "column1");

        $this->assertSame("/list?orderColumn=column1&orderDirection=desc", $url);
    }

    public function testCopiesRelevantQueryParams(): void
    {
        $request = $this->createRequestStubForList(["filterColumn" => "bar", "filterValue" => "baz"]);

        $url = $this->orderingUrlGenerator->createOrderingUrl($request, "column1");

        $this->assertStringContainsString("filterColumn=bar", $url);
        $this->assertStringContainsString("filterValue=baz", $url);
    }

    public function testSkipsIrreleventQueryParams(): void
    {
        $request = $this->createRequestStubForList(["qux" => "bar"]);

        $url = $this->orderingUrlGenerator->createOrderingUrl($request, "column1");

        $this->assertStringNotContainsString("qux", $url);
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
