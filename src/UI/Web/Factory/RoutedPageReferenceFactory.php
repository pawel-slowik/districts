<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

class RoutedPageReferenceFactory
{
    private $pageReferenceFactory;

    private $routeParser;

    public function __construct(
        PageReferenceFactory $pageReferenceFactory,
        RouteParserInterface $routeParser
    ) {
        $this->pageReferenceFactory = $pageReferenceFactory;
        $this->routeParser = $routeParser;
    }

    public function createPageReferences(
        ServerRequestInterface $namedRouteRequest,
        int $pageCount,
        int $currentPageNumber
    ): \Traversable {
        return $this->pageReferenceFactory->createPageReferences(
            $this->createBaseUrlForPagination($namedRouteRequest),
            $pageCount,
            $currentPageNumber,
        );
    }

    private function createBaseUrlForPagination(ServerRequestInterface $namedRouteRequest): string
    {
        $routeContext = RouteContext::fromRequest($namedRouteRequest);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new \InvalidArgumentException();
        }
        if (is_null($route->getName())) {
            throw new \InvalidArgumentException();
        }
        return $this->routeParser->fullUrlFor(
            $namedRouteRequest->getUri(),
            $route->getName(),
            $route->getArguments(),
            $namedRouteRequest->getQueryParams(),
        );
    }
}
