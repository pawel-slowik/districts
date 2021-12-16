<?php

declare(strict_types=1);

namespace Districts\UI\Web\View;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

class OrderingUrlGenerator
{
    private RouteParserInterface $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function createOrderingUrl(
        ServerRequestInterface $namedRouteRequest,
        string $column,
        array $routeArgs
    ): string {
        return $this->routeParser->urlFor(
            $this->getRouteNameFromRequest($namedRouteRequest),
            [
                "column" => $column,
                "direction" => $this->computeOrderingDirection($column, $routeArgs),
            ],
            $this->copyRelevantQueryParams($namedRouteRequest->getQueryParams())
        );
    }

    private function getRouteNameFromRequest(ServerRequestInterface $namedRouteRequest): string
    {
        $routeContext = RouteContext::fromRequest($namedRouteRequest);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new InvalidArgumentException();
        }
        if (is_null($route->getName())) {
            throw new InvalidArgumentException();
        }
        return $route->getName();
    }

    private function computeOrderingDirection(string $column, array $routeArgs): string
    {
        if (array_key_exists("column", $routeArgs)
            && array_key_exists("direction", $routeArgs)
            && ($routeArgs["column"] === $column)
            && ($routeArgs["direction"] === "asc")
        ) {
            return "desc";
        }
        return "asc";
    }

    private function copyRelevantQueryParams(array $queryParams): array
    {
        $newQueryParams = [];
        if (array_key_exists("filterColumn", $queryParams)) {
            $newQueryParams["filterColumn"] = $queryParams["filterColumn"];
        }
        if (array_key_exists("filterValue", $queryParams)) {
            $newQueryParams["filterValue"] = $queryParams["filterValue"];
        }
        return $newQueryParams;
    }
}
