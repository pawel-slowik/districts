<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Domain\District;
use Districts\Editor\UI\Factory\ListDistrictsQueryFactory;
use Districts\Editor\UI\Session;
use Districts\Editor\UI\View\ListView;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ListController
{
    /**
     * @param ListView<District> $listView
     */
    public function __construct(
        private DistrictService $districtService,
        private ListDistrictsQueryFactory $queryFactory,
        private Session $session,
        private ListView $listView,
    ) {
    }

    /**
     * @param array<string, string> $args
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $query = $this->queryFactory->fromRequest($request);
        } catch (InvalidArgumentException) {
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
            "districts" => $districts->currentPageEntries,
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
