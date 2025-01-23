<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Districts\Editor\Domain\PaginatedResult;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @template T
 */
class ListTemplater
{
    public function __construct(
        private PageReferenceFactory $pageReferenceFactory,
        private OrderingUrlGenerator $orderingUrlGenerator,
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
        $data = [
            "title" => $title,
            "successMessage" => $successMessage,
            "errorMessage" => $errorMessage,
        ];

        $data["entries"] = $paginatedResult->currentPageEntries;

        $data["orderingUrls"] = $this->createOrderingUrls(
            $request,
            $orderingColumns
        );

        $requestUrl = $request->getUri();
        $relativeUrl = ($this->uriFactory->createUri())
            ->withPath($requestUrl->getPath())
            ->withQuery($requestUrl->getQuery()); // query parameters must be preserved for filtering

        $data["pagination"] = iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForUrl(
                $relativeUrl,
                $paginatedResult->pageCount,
                $paginatedResult->pagination->pageNumber
            )
        );

        $queryParams = $request->getQueryParams();
        $data["filterColumn"] = $queryParams["filterColumn"] ?? null;
        $data["filterValue"] = $queryParams["filterValue"] ?? null;

        return $data;
    }

    /**
     * @param string[] $columns
     *
     * @return array<string, string>
     */
    private function createOrderingUrls(
        ServerRequestInterface $request,
        array $columns
    ): array {
        $urls = [];
        foreach ($columns as $column) {
            $urls[$column] = $this->orderingUrlGenerator->createOrderingUrl($request, $column);
        }
        return $urls;
    }
}
