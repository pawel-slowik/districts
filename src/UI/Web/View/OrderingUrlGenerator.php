<?php

declare(strict_types=1);

namespace Districts\UI\Web\View;

use Slim\Interfaces\RouteParserInterface;

class OrderingUrlGenerator
{
    private $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function createOrderingUrl(string $routeName, string $column, array $routeArgs, array $queryParams): string
    {
        return $this->routeParser->urlFor(
            $routeName,
            [
                "column" => $column,
                "direction" => $this->computeOrderingDirection($column, $routeArgs),
            ],
            $this->copyRelevantQueryParams($queryParams)
        );
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
