<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\UI\View\Filter;
use Districts\Editor\UI\View\ListTemplater;
use Districts\Editor\UI\View\OrderingLink;
use Districts\Editor\UI\View\OrderingLinkGenerator;
use Districts\Editor\UI\View\PageReferenceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

#[CoversClass(ListTemplater::class)]
class ListTemplaterTest extends TestCase
{
    /** @var ListTemplater<string> */
    private ListTemplater $listTemplater;

    /** @var PageReferenceFactory&Stub */
    private PageReferenceFactory $pageReferenceFactory;

    /** @var OrderingLinkGenerator&Stub */
    private OrderingLinkGenerator $orderingLinkGenerator;

    /** @var Stub&UriFactoryInterface */
    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->pageReferenceFactory = $this->createStub(PageReferenceFactory::class);
        $this->orderingLinkGenerator = $this->createStub(OrderingLinkGenerator::class);
        $this->uriFactory = $this->createStub(UriFactoryInterface::class);

        $this->listTemplater = new ListTemplater(
            $this->pageReferenceFactory,
            $this->orderingLinkGenerator,
            $this->uriFactory,
        );
    }

    public function testTitle(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $this->createStub(ServerRequestInterface::class),
            [],
            "search results",
            null,
            null,
        );

        $this->assertArrayHasKey("title", $templateData);
        $this->assertIsString($templateData["title"]);
    }

    public function testSuccessMessage(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $this->createStub(ServerRequestInterface::class),
            [],
            "",
            "success :)",
            null,
        );

        $this->assertArrayHasKey("successMessage", $templateData);
        $this->assertIsString($templateData["successMessage"]);
    }

    public function testErrorMessage(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $this->createStub(ServerRequestInterface::class),
            [],
            "",
            null,
            "error :(",
        );

        $this->assertArrayHasKey("errorMessage", $templateData);
        $this->assertIsString($templateData["errorMessage"]);
    }

    public function testEntries(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, ["a", "b", "c"]),
            $this->createStub(ServerRequestInterface::class),
            [],
            "",
            null,
            null,
        );

        $this->assertArrayHasKey("entries", $templateData);
        $this->assertSame(["a", "b", "c"], $templateData["entries"]);
    }

    public function testOrderingLinks(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $this->createStub(ServerRequestInterface::class),
            ["foo", "bar"],
            "",
            null,
            null,
        );

        $this->assertArrayHasKey("orderingLinks", $templateData);
        $this->assertIsArray($templateData["orderingLinks"]);
        $this->assertCount(2, $templateData["orderingLinks"]);
        $this->assertArrayHasKey("foo", $templateData["orderingLinks"]);
        $this->assertArrayHasKey("bar", $templateData["orderingLinks"]);
        $this->assertInstanceOf(OrderingLink::class, $templateData["orderingLinks"]["foo"]);
        $this->assertInstanceOf(OrderingLink::class, $templateData["orderingLinks"]["bar"]);
    }

    public function testPagination(): void
    {
        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $this->createStub(ServerRequestInterface::class),
            [],
            "",
            null,
            null,
        );

        $this->assertArrayHasKey("pagination", $templateData);
        $this->assertIsArray($templateData["pagination"]);
    }

    public function testFilter(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method("getQueryParams")
            ->willReturn(["filterColumn" => "x", "filterValue" => "y"]);

        $templateData = $this->listTemplater->prepareTemplateData(
            new PaginatedResult(new Pagination(1, 1), 1, 1, []),
            $request,
            [],
            "",
            null,
            null,
        );

        $this->assertArrayHasKey("filter", $templateData);
        $this->assertInstanceOf(Filter::class, $templateData["filter"]);
        $this->assertSame("x", $templateData["filter"]->column);
        $this->assertSame("y", $templateData["filter"]->value);
    }
}
