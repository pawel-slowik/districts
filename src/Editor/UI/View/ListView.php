<?php

declare(strict_types=1);

namespace Districts\Editor\UI\View;

use Districts\Editor\Domain\PaginatedResult;
use Laminas\Uri\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
        $data["orderingUrls"] = $this->createOrderingUrls(
            $request,
            $orderingColumns
        );

        $requestUrl = $request->getUri();
        $relativeUrl = (new Uri())
            ->setPath($requestUrl->getPath())
            ->setQuery($requestUrl->getQuery()); // query parameters must be preserved for filtering

        $data["pagination"] = iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForUrl(
                $relativeUrl->toString(),
                $paginatedResult->getPageCount(),
                $paginatedResult->getCurrentPageNumber()
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
