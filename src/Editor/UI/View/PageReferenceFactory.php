<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use InvalidArgumentException;
use Laminas\Uri\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Traversable;

class PageReferenceFactory
{
    public function __construct(
        private RouteParserInterface $routeParser,
    ) {
    }

    public function createPageReferencesForNamedRouteRequest(
        ServerRequestInterface $namedRouteRequest,
        int $pageCount,
        int $currentPageNumber
    ): Traversable {
        $baseUrl = $this->baseUrlForNamedRouteRequest($namedRouteRequest);
        return $this->createPageReferencesForUrl(
            $baseUrl,
            $pageCount,
            $currentPageNumber,
        );
    }

    public function createPageReferencesForUrl(
        string $baseUrl,
        int $pageCount,
        int $currentPageNumber
    ): Traversable {
        if ($pageCount <= 1) {
            return;
        }
        yield new PageReference(
            ($currentPageNumber === 1) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber - 1),
            "previous",
            false,
            true,
            false,
        );
        foreach (range(1, $pageCount) as $pageNumber) {
            yield new PageReference(
                self::urlForPageNumber($baseUrl, $pageNumber),
                strval($pageNumber),
                $pageNumber === $currentPageNumber,
                false,
                false,
            );
        }
        yield new PageReference(
            ($currentPageNumber === $pageCount) ? null : self::urlForPageNumber($baseUrl, $currentPageNumber + 1),
            "next",
            false,
            false,
            true,
        );
    }

    private function baseUrlForNamedRouteRequest(ServerRequestInterface $namedRouteRequest): string
    {
        $routeContext = RouteContext::fromRequest($namedRouteRequest);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new InvalidArgumentException();
        }
        if (is_null($route->getName())) {
            throw new InvalidArgumentException();
        }
        return $this->routeParser->urlFor(
            $route->getName(),
            $route->getArguments(),
            $namedRouteRequest->getQueryParams(),
        );
    }

    private static function urlForPageNumber(string $baseUrl, int $pageNumber): string
    {
        $parsedUrl = new Uri($baseUrl);
        $parsedUrl->setQuery(array_merge($parsedUrl->getQueryAsArray(), ["page" => $pageNumber]));
        return $parsedUrl->toString();
    }
}
