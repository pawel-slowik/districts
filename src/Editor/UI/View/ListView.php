<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Districts\Editor\Domain\PaginatedResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\Views\Twig as View;

/**
 * @template T
 */
class ListView
{
    public function __construct(
        private View $view,
        private PageReferenceFactory $pageReferenceFactory,
        private OrderingUrlGenerator $orderingUrlGenerator,
        private UriFactoryInterface $uriFactory,
    ) {
    }

    /**
     * @param PaginatedResult<T> $paginatedResult
     * @param string[] $orderingColumns
     * @param array<string, mixed> $data
     */
    public function render(
        ResponseInterface $response,
        PaginatedResult $paginatedResult,
        ServerRequestInterface $request,
        array $orderingColumns,
        string $template,
        array $data = []
    ): ResponseInterface {
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

        return $this->view->render($response, $template, $data);
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
