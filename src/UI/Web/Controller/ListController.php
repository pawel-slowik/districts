<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;

use Service\DistrictService;
use UI\Web\DistrictFilterFactory;
use UI\Web\DistrictOrderingFactory;

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
        $queryParams = $request->getQueryParams();
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        $districts = $this->districtService->list(
            DistrictOrderingFactory::createFromRequestInput($orderColumn, $orderDirection),
            DistrictFilterFactory::createFromRequestInput($filterColumn, $filterValue),
        );
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
