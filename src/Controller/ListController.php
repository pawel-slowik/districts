<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Repository\DistrictRepository;

class ListController extends BaseCrudController
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args)
    {
        $orderColumn = $args["column"] ?? null;
        $orderDirection = $args["direction"] ?? null;
        $orderBy = $this->repositoryOrderBy($orderColumn, $orderDirection);
        $districts = $this->repository->list($orderBy);
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts,
            "orderColumn" => $orderColumn,
            "orderDirection" => $orderDirection,
        ];
        return $this->view->render($response, "list.html", $templateData);
    }

    protected function repositoryOrderBy(?string $orderColumn, ?string $orderDirection)
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
