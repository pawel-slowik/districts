<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Slim\Routing\RoutingResults;

use Districts\UI\Web\PageReference;
use Districts\UI\Web\Factory\PageReferenceFactory;
use Districts\UI\Web\Factory\RoutedPageReferenceFactory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Factory\RoutedPageReferenceFactory
 */
class RoutedPageReferenceFactoryTest extends TestCase
{
    /**
     * @var RoutedPageReferenceFactory
     */
    private $routedPageReferenceFactory;

    /**
     * @var MockObject|ServerRequestInterface
     */
    private $validRequest;

    /**
     * @var MockObject|ServerRequestInterface
     */
    private $unroutedRequest;

    /**
     * @var MockObject|ServerRequestInterface
     */
    private $unnamedRouteRequest;

    protected function setUp(): void
    {
        $this->validRequest = $this->createRequestMockWithAttributes($this->createRequestAttributes("test"));
        $this->unroutedRequest = $this->createRequestMockWithAttributes(
            array_merge(
                $this->createRequestAttributes("test"),
                [RouteContext::ROUTE => null],
            )
        );
        $this->unnamedRouteRequest = $this->createRequestMockWithAttributes($this->createRequestAttributes(null));

        $pageReferenceFactory = $this->createMock(PageReferenceFactory::class);
        $pageReferenceFactory->method("createPageReferences")->willReturn(
            new \ArrayIterator(
                [
                    $this->createMock(PageReference::class),
                    $this->createMock(PageReference::class),
                ]
            )
        );
        $this->routedPageReferenceFactory = new RoutedPageReferenceFactory(
            $pageReferenceFactory,
            $this->createMock(RouteParserInterface::class)
        );
    }

    public function testValidRequest(): void
    {
        $pageReferences = $this->routedPageReferenceFactory->createPageReferences($this->validRequest, 1, 1);
        $this->assertContainsOnlyInstancesOf(PageReference::class, iterator_to_array($pageReferences));
        $this->assertCount(2, $pageReferences);
    }

    public function testExceptionOnUnroutedRequest(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->routedPageReferenceFactory->createPageReferences($this->unroutedRequest, 1, 1);
    }

    public function testExceptionOnUnnamedRoute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->routedPageReferenceFactory->createPageReferences($this->unnamedRouteRequest, 1, 1);
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
