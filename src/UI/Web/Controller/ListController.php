<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\View\ListView;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSession\Helper as Session;

final class ListController
{
    private DistrictService $districtService;

    private ListDistrictsQueryFactory $queryFactory;

    private Session $session;

    private ListView $listView;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        Session $session,
        ListView $listView
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->listView = $listView;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $query = $this->queryFactory->fromRequest($request, $args);
        } catch (InvalidArgumentException $exception) {
            $query = $this->queryFactory->fromDefaults();
            $errorMessage = "Invalid query parameters";
        }
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
        $this->listView->configurePagination($pageCount, $currentPageNumber, $request);
        $this->listView->configureOrdering("list", $orderingColumns, $args, $queryParams);
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts->getCurrentPageEntries(),
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "successMessage" => $this->session["success.message"],
            "errorMessage" => $errorMessage ?? null,
        ];
        unset($this->session["success.message"]);
        return $this->listView->render($response, "list.html", $templateData);
    }
}
