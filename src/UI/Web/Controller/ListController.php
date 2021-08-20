<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\ListView;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSession\Helper as Session;

final class ListController
{
    private $districtService;

    private $queryFactory;

    private $session;

    private $view;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        Session $session,
        ListView $view
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->view = $view;
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
        $this->view->configurePagination($pageCount, $currentPageNumber, $request);
        $this->view->configureOrdering("list", $orderingColumns, $args, $queryParams);
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts->getCurrentPageEntries(),
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "successMessage" => $this->session["success.message"],
        ];
        unset($this->session["success.message"]);
        return $this->view->render($response, "list.html", $templateData);
    }
}
