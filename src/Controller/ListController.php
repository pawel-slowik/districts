<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Repository\DistrictRepository;

final class ListController extends BaseCrudController
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args)
    {
        $orderColumn = $args["column"] ?? null;
        $orderDirection = $args["direction"] ?? null;
        $orderBy = $this->repositoryOrderBy($orderColumn, $orderDirection);

        $queryParams = $request->getQueryParams();
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        list($repositoryFilterType, $repositoryFilterValue) = $this->repositoryFilter($filterColumn, $filterValue);

        $districts = $this->districtRepository->list($orderBy, $repositoryFilterType, $repositoryFilterValue);
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

    private function repositoryFilter(?string $filterColumn, ?string $filterValue): array
    {
        switch ($filterColumn) {
            case "city":
                return [
                    DistrictRepository::FILTER_CITY,
                    strval($filterValue),
                ];
            case "name":
                return [
                    DistrictRepository::FILTER_NAME,
                    strval($filterValue),
                ];
            case "area":
                return [
                    DistrictRepository::FILTER_AREA,
                    $this->filterStringToRange($filterValue),
                ];
            case "population":
                return [
                    DistrictRepository::FILTER_POPULATION,
                    $this->filterStringToRange($filterValue),
                ];
        }
        return [DistrictRepository::FILTER_NONE, null];
    }

    private function filterStringToRange(string $input): array
    {
        $range = array_map("floatval", explode("-", $input, 2));
        if (count($range) < 2) {
            $range[1] = $range[0];
        }
        return $range;
    }

    private function repositoryOrderBy(?string $orderColumn, ?string $orderDirection)
    {
        $rules = [
            ["city", "asc", DistrictRepository::ORDER_CITY_ASC],
            ["city", "desc", DistrictRepository::ORDER_CITY_DESC],
            ["name", "asc", DistrictRepository::ORDER_NAME_ASC],
            ["name", "desc", DistrictRepository::ORDER_NAME_DESC],
            ["area", "asc", DistrictRepository::ORDER_AREA_ASC],
            ["area", "desc", DistrictRepository::ORDER_AREA_DESC],
            ["population", "asc", DistrictRepository::ORDER_POPULATION_ASC],
            ["population", "desc", DistrictRepository::ORDER_POPULATION_DESC],
        ];
        foreach ($rules as $rule) {
            if ([$orderColumn, $orderDirection] === [$rule[0], $rule[1]]) {
                return $rule[2];
            }
        }
        return DistrictRepository::ORDER_DEFAULT;
    }
}
