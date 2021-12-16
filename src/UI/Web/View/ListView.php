<?php

declare(strict_types=1);

namespace Districts\UI\Web\View;

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

    private ServerRequestInterface $paginationRequest;

    private string $orderingRouteName;

    /**
     * @var string[]
     */
    private array $orderingColumns;

    /**
     * @var string[]
     */
    private array $orderingRouteArgs;

    /**
     * @var string[]
     */
    private array $orderingQueryParams;

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
        int $pageCount,
        int $currentPageNumber,
        ServerRequestInterface $request,
        string $routeName,
        array $columns,
        array $routeArgs,
        array $queryParams
    ): void {
        $this->paginationPageCount = $pageCount;
        $this->paginationCurrentPageNumber = $currentPageNumber;
        $this->paginationRequest = $request;
        $this->orderingRouteName = $routeName;
        $this->orderingColumns = $columns;
        $this->orderingRouteArgs = $routeArgs;
        $this->orderingQueryParams = $queryParams;
    }

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $data["orderingUrls"] = $this->createOrderingUrls(
            $this->orderingRouteName,
            $this->orderingColumns,
            $this->orderingRouteArgs,
            $this->orderingQueryParams
        );

        $data["pagination"] = iterator_to_array(
            $this->pageReferenceFactory->createPageReferencesForNamedRouteRequest(
                $this->paginationRequest,
                $this->paginationPageCount,
                $this->paginationCurrentPageNumber,
            )
        );

        return $this->view->render($response, $template, $data);
    }

    private function createOrderingUrls(string $routeName, array $columns, array $args, array $queryParams): array
    {
        $urls = [];
        foreach ($columns as $column) {
            $urls[$column] = $this->orderingUrlGenerator->createOrderingUrl($routeName, $column, $args, $queryParams);
        }
        return $urls;
    }
}
