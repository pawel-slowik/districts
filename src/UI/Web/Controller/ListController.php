<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;

use Service\DistrictService;
use Service\DistrictFilterFactory;

final class ListController
{
    private $districtService;

    private $view;

    public function __construct(
        DistrictService $districtService,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->view = $view;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $orderColumn = $args["column"] ?? null;
        $orderDirection = $args["direction"] ?? null;
        $orderBy = $this->serviceOrderBy($orderColumn, $orderDirection);

        $queryParams = $request->getQueryParams();
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        $filter = DistrictFilterFactory::createFromRequestInput($filterColumn, $filterValue);

        $districts = $this->districtService->listDistricts($orderBy, $filter);
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts,
            "orderColumn" => $orderColumn,
            "orderDirection" => $orderDirection,
            "filterColumn" => $filterColumn,
            "filterValue" => $filterValue,
        ];
        return $this->view->render($response, "list.html", $templateData);
    }

    private function serviceOrderBy(?string $orderColumn, ?string $orderDirection): int
    {
        $rules = [
            ["city", "asc", DistrictService::ORDER_CITY_ASC],
            ["city", "desc", DistrictService::ORDER_CITY_DESC],
            ["name", "asc", DistrictService::ORDER_NAME_ASC],
            ["name", "desc", DistrictService::ORDER_NAME_DESC],
            ["area", "asc", DistrictService::ORDER_AREA_ASC],
            ["area", "desc", DistrictService::ORDER_AREA_DESC],
            ["population", "asc", DistrictService::ORDER_POPULATION_ASC],
            ["population", "desc", DistrictService::ORDER_POPULATION_DESC],
        ];
        foreach ($rules as $rule) {
            if ([$orderColumn, $orderDirection] === [$rule[0], $rule[1]]) {
                return $rule[2];
            }
        }
        return DistrictService::ORDER_DEFAULT;
    }
}
