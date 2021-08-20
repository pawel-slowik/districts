<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\Factory\RoutedPageReferenceFactory;
use Districts\UI\Web\OrderingUrlGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

final class ListController
{
    private $districtService;

    private $queryFactory;

    private $session;

    private $view;

    private $pageReferenceFactory;

    private $orderingUrlGenerator;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        Session $session,
        View $view,
        RoutedPageReferenceFactory $pageReferenceFactory,
        OrderingUrlGenerator $orderingUrlGenerator
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->view = $view;
        $this->pageReferenceFactory = $pageReferenceFactory;
        $this->orderingUrlGenerator = $orderingUrlGenerator;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $query = $this->queryFactory->fromRequest($request, $args);
        $districts = $this->districtService->list($query);
        $queryParams = $request->getQueryParams();
        $pageCount = $districts->getPageCount();
        $currentPageNumber = is_null($query->getPagination()) ? 1 : $query->getPagination()->getPageNumber();
        $orderingColumns = [
            "city",
            "name",
            "area",
            "population",
        ];
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts->getCurrentPageEntries(),
            "orderingUrls" => $this->createOrderingUrls("list", $orderingColumns, $args, $queryParams),
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "pagination" => iterator_to_array(
                $this->pageReferenceFactory->createPageReferences(
                    $request,
                    $pageCount,
                    $currentPageNumber,
                )
            ),
            "successMessage" => $this->session["success.message"],
        ];
        unset($this->session["success.message"]);
        return $this->view->render($response, "list.html", $templateData);
    }

    private function createOrderingUrls(string $routeName, array $columns, array $args, array $queryParams): array
    {
        return array_combine(
            $columns,
            array_map(
                function (string $column) use ($routeName, $args, $queryParams): string {
                    return $this->orderingUrlGenerator->createOrderingUrl($routeName, $column, $args, $queryParams);
                },
                $columns
            )
        );
    }
}
