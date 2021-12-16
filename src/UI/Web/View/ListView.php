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

    private int $paginationPageCount;

    private int $paginationCurrentPageNumber;

    private ServerRequestInterface $request;

    /**
     * @var string[]
     */
    private array $orderingColumns;

    /**
     * @var string[]
     */
    private array $orderingRouteArgs;

    public function __construct(
        View $view,
        PageReferenceFactory $pageReferenceFactory,
        OrderingUrlGenerator $orderingUrlGenerator
    ) {
        $this->view = $view;
        $this->pageReferenceFactory = $pageReferenceFactory;
        $this->orderingUrlGenerator = $orderingUrlGenerator;
    }

    public function configure(
        PaginatedResult $paginatedResult,
        ServerRequestInterface $request,
        array $columns,
        array $routeArgs
    ): void {
        $this->paginationPageCount = $paginatedResult->getPageCount();
        $this->paginationCurrentPageNumber = $paginatedResult->getCurrentPageNumber();
        $this->request = $request;
        $this->orderingColumns = $columns;
        $this->orderingRouteArgs = $routeArgs;
    }

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $data["orderingUrls"] = $this->createOrderingUrls(
            $this->request,
            $this->orderingColumns,
            $this->orderingRouteArgs
        );

        $data["pagination"] = iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest(
                $this->request,
                $this->paginationPageCount,
                $this->paginationCurrentPageNumber,
            )
        );

        return $this->view->render($response, $template, $data);
    }

    private function createOrderingUrls(
        ServerRequestInterface $request,
        array $columns,
        array $args
    ): array {
        $urls = [];
        foreach ($columns as $column) {
            $urls[$column] = $this->orderingUrlGenerator->createOrderingUrl($request, $column, $args);
        }
        return $urls;
    }
}
