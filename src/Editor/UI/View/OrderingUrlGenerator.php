<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Laminas\Uri\Uri;
use Psr\Http\Message\ServerRequestInterface;

class OrderingUrlGenerator
{
    public function createOrderingUrl(ServerRequestInterface $request, string $column): string
    {
        $queryParams = $request->getQueryParams();
        $orderingQueryParams = [
            "orderColumn" => $column,
            "orderDirection" => $this->computeOrderingDirection($column, $queryParams),
        ];
        $updatedQueryParams = array_merge(
            $orderingQueryParams,
            $this->copyRelevantQueryParams($queryParams),
        );
        $uri = (new Uri())
            ->setPath($request->getUri()->getPath())
            ->setQuery($updatedQueryParams);
        return $uri->toString();
    }

    /**
     * @param array<string, string> $queryParams
     */
    private function computeOrderingDirection(string $column, array $queryParams): string
    {
        if (
            array_key_exists("orderColumn", $queryParams)
            && array_key_exists("orderDirection", $queryParams)
            && ($queryParams["orderColumn"] === $column)
            && ($queryParams["orderDirection"] === "asc")
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
