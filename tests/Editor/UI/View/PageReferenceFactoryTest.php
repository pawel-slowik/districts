<?php

declare(strict_types=1);

namespace Districts\Test\Editor\UI\View;

use Districts\Editor\UI\View\PageReference;
use Districts\Editor\UI\View\PageReferenceFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

/**
 * @covers \Districts\Editor\UI\View\PageReferenceFactory
 */
class PageReferenceFactoryTest extends TestCase
{
    use NamedRequestTester;

    private PageReferenceFactory $pageReferenceFactory;

    private RouteParserInterface $routeParser;

    protected function setUp(): void
    {
        $this->routeParser = $this->createStub(RouteParserInterface::class);
        $this->pageReferenceFactory = new PageReferenceFactory($this->routeParser);
    }

    public function testType(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 5, 1);
        $this->assertContainsOnlyInstancesOf(PageReference::class, $pageReferences);
    }

    public function testSinglePage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 1, 1);
        $this->assertCount(0, iterator_to_array($pageReferences));
    }

    public function testNumberOfReferences(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 100, 1);
        $this->assertCount(102, iterator_to_array($pageReferences));
    }

    /**
     * @dataProvider flagsProvider
     */
    public function testFlags(string $baseUrl, int $pageCount, int $currentPageNumber): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl(
            $baseUrl,
            $pageCount,
            $currentPageNumber
        );
        $materializedPageReferences = iterator_to_array($pageReferences);
        $current = [];
        foreach ($materializedPageReferences as $pageReference) {
            if ($pageReference->isCurrent()) {
                $current[] = $pageReference;
            }
        }
        $first = $materializedPageReferences[0];
        $last = $materializedPageReferences[array_key_last($materializedPageReferences)];

        $this->assertCount(1, $current);
        $this->assertTrue($first->isPrevious());
        $this->assertTrue($last->isNext());
    }

    public static function flagsProvider(): array
    {
        return [
            ["https://example.com/", 10, 1],
            ["https://example.com/", 10, 5],
            ["https://example.com/", 10, 10],
        ];
    }

    public function testPreviousUrlForFirstPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 3, 1);
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isPrevious()) {
                $this->assertNull($pageReference->getUrl());
            }
        }
    }

    public function testNextUrlForLastPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 3, 3);
        foreach ($pageReferences as $pageReference) {
            if ($pageReference->isNext()) {
                $this->assertNull($pageReference->getUrl());
            }
        }
    }

    public function testUrlsForInBetweenPage(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 3, 2);
        $urls = [];
        foreach ($pageReferences as $pageReference) {
            $urls[] = $pageReference->getUrl();
        }

        $this->assertSame(
            [
                "https://example.com/?page=1",
                "https://example.com/?page=1",
                "https://example.com/?page=2",
                "https://example.com/?page=3",
                "https://example.com/?page=3",
            ],
            $urls,
        );
    }

    public function testCurrentOffset(): void
    {
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForUrl("https://example.com/", 20, 10);
        $currentOffset = null;
        foreach ($pageReferences as $offset => $pageReference) {
            if ($pageReference->isCurrent()) {
                $currentOffset = $offset;

                break;
            }
        }

        $this->assertSame(10, $currentOffset);
    }

    public function testValidRequest(): void
    {
        $validRequest = $this->createRequestMockWithAttributes($this->createRequestAttributes("test"));
        $pageReferences = $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest($validRequest, 2, 2);
        $materializedPageReferences = iterator_to_array($pageReferences);
        $this->assertContainsOnlyInstancesOf(PageReference::class, $materializedPageReferences);
        $this->assertCount(4, $materializedPageReferences);
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
        $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest($unroutedRequest, 1, 1);
    }

    public function testExceptionOnUnnamedRoute(): void
    {
        $unnamedRouteRequest = $this->createRequestMockWithAttributes($this->createRequestAttributes(null));
        $this->expectException(InvalidArgumentException::class);
        $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest($unnamedRouteRequest, 1, 1);
    }
}
