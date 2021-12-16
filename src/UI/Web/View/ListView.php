<?php

declare(strict_types=1);

namespace Districts\UI\Web\View;

use Districts\DomainModel\PaginatedResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig as View;

class ListView
{
    private View $view;

    private PageReferenceFactory $pageReferenceFactory;

    private OrderingUrlGenerator $orderingUrlGenerator;

    public function __construct(
        View $view,
        PageReferenceFactory $pageReferenceFactory,
        OrderingUrlGenerator $orderingUrlGenerator
    ) {
        $this->view = $view;
        $this->pageReferenceFactory = $pageReferenceFactory;
        $this->orderingUrlGenerator = $orderingUrlGenerator;
    }

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

        $data["pagination"] = iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest(
                $request,
                $paginatedResult->getPageCount(),
                $paginatedResult->getCurrentPageNumber()
            )
        );

        return $this->view->render($response, $template, $data);
    }

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
