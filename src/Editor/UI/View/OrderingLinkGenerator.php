<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

readonly class OrderingLinkGenerator
{
    public function __construct(
        private UriFactoryInterface $uriFactory,
    ) {
    }

    public function createOrderingLink(ServerRequestInterface $request, string $column): OrderingLink
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
        $uri = $this->uriFactory->createUri()
            ->withPath($request->getUri()->getPath())
            ->withQuery(http_build_query($updatedQueryParams));

        if (!$this->isOrderedByColumn($column, $queryParams)) {
            return OrderingLink::createUnordered($uri);
        }
        return $this->isOrderedAscending($queryParams)
            ? OrderingLink::createOrderedAscending($uri)
            : OrderingLink::createOrderedDescending($uri);
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
     */
    private static function isOrderedByColumn(string $column, array $queryParams): bool
    {
        return array_key_exists("orderColumn", $queryParams) && ($queryParams["orderColumn"] === $column);
    }

    /**
     * @param array<string, string> $queryParams
     */
    private static function isOrderedAscending(array $queryParams): bool
    {
        return array_key_exists("orderDirection", $queryParams) && ($queryParams["orderDirection"] === "asc");
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
