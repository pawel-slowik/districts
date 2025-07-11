<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Districts\Editor\Domain\PaginatedResult;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @template T
 */
final readonly class ListTemplater
{
    public function __construct(
        private PageReferenceFactory $pageReferenceFactory,
        private OrderingLinkGenerator $orderingLinkGenerator,
        private UriFactoryInterface $uriFactory,
    ) {
    }

    /**
     * @param PaginatedResult<T> $paginatedResult
     * @param string[] $orderingColumns
     *
     * @return array<string, mixed>
     */
    public function prepareTemplateData(
        PaginatedResult $paginatedResult,
        ServerRequestInterface $request,
        array $orderingColumns,
        string $title,
        mixed $successMessage,
        mixed $errorMessage,
    ): array {
        return [
            "title" => $title,
            "successMessage" => $successMessage,
            "errorMessage" => $errorMessage,
            "entries" => $paginatedResult->currentPageEntries,
            "orderingLinks" => $this->createOrderingLinks($request, $orderingColumns),
            "pagination" => $this->createPagination($request, $paginatedResult),
            "filter" => $this->createFilter($request),
        ];
    }

    /**
     * @param string[] $columns
     *
     * @return array<string, OrderingLink>
     */
    private function createOrderingLinks(ServerRequestInterface $request, array $columns): array
    {
        $links = [];
        foreach ($columns as $column) {
            $links[$column] = $this->orderingLinkGenerator->createOrderingLink($request, $column);
        }
        return $links;
    }

    /**
     * @param PaginatedResult<T> $paginatedResult
     *
     * @return PageReference[]
     */
    private function createPagination(ServerRequestInterface $request, PaginatedResult $paginatedResult): array
    {
        $requestUrl = $request->getUri();
        $relativeUrl = ($this->uriFactory->createUri())
            ->withPath($requestUrl->getPath())
            ->withQuery($requestUrl->getQuery()); // query parameters must be preserved for filtering
        return iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForUrl(
                $relativeUrl,
                $paginatedResult->pageCount,
                $paginatedResult->pagination->pageNumber
            )
        );
    }

    private function createFilter(ServerRequestInterface $request): Filter
    {
        $queryParams = $request->getQueryParams();
        return new Filter(
            $queryParams["filterColumn"] ?? null,
            $queryParams["filterValue"] ?? null,
        );
    }
}
