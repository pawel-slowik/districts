<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;

use Service\DistrictService;
use Service\DistrictFilterFactory;
use Service\DistrictOrderingFactory;

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
        $order = DistrictOrderingFactory::createFromRequestInput($orderColumn, $orderDirection);

        $queryParams = $request->getQueryParams();
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        $filter = DistrictFilterFactory::createFromRequestInput($filterColumn, $filterValue);

        $districts = $this->districtService->listDistricts($order, $filter);
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
}
