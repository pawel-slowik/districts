<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\Session;
use Districts\UI\Web\View\ListView;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ListController
{
    public function __construct(
        private DistrictService $districtService,
        private ListDistrictsQueryFactory $queryFactory,
        private Session $session,
        private ListView $listView,
    ) {
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
        $orderingColumns = [
            "city",
            "name",
            "area",
            "population",
        ];
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts->getCurrentPageEntries(),
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "successMessage" => $this->session->getAndDelete("success.message"),
            "errorMessage" => $errorMessage ?? null,
        ];
        return $this->listView->render(
            $response,
            $districts,
            $request,
            $orderingColumns,
            "list.html",
            $templateData
        );
    }
}
