<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

class OrderingUrlGenerator
{
    public function __construct(
        private RouteParserInterface $routeParser,
    ) {
    }

    public function createOrderingUrl(
        ServerRequestInterface $namedRouteRequest,
        string $column
    ): string {
        return $this->routeParser->urlFor(
            $this->getRouteNameFromRequest($namedRouteRequest),
            [
                "column" => $column,
                "direction" => $this->computeOrderingDirection(
                    $column,
                    $this->getRouteArgumentsFromRequest($namedRouteRequest)
                ),
            ],
            $this->copyRelevantQueryParams($namedRouteRequest->getQueryParams())
        );
    }

    private function getRouteNameFromRequest(ServerRequestInterface $namedRouteRequest): string
    {
        $route = $this->getRouteFromRequest($namedRouteRequest);
        if (is_null($route->getName())) {
            throw new InvalidArgumentException();
        }
        return $route->getName();
    }

    /**
     * @return array<string, string>
     */
    private function getRouteArgumentsFromRequest(ServerRequestInterface $request): array
    {
        return $this->getRouteFromRequest($request)->getArguments();
    }

    private function getRouteFromRequest(ServerRequestInterface $routedRequest): RouteInterface
    {
        $routeContext = RouteContext::fromRequest($routedRequest);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new InvalidArgumentException();
        }
        return $route;
    }

    /**
     * @param array<string, string> $routeArgs
     */
    private function computeOrderingDirection(string $column, array $routeArgs): string
    {
        if (
            array_key_exists("column", $routeArgs)
            && array_key_exists("direction", $routeArgs)
            && ($routeArgs["column"] === $column)
            && ($routeArgs["direction"] === "asc")
        ) {
            return "desc";
        }
        return "asc";
    }

    /**
     * @param array<string, string> $queryParams
     *
     * @return array<string, string>
     */
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
