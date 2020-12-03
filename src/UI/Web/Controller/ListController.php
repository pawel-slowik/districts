<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;

use Districts\Service\DistrictService;
use Districts\UI\Web\Factory\DistrictFilterFactory;
use Districts\UI\Web\Factory\DistrictOrderingFactory;

final class ListController
{
    private $districtService;

    private $filterFactory;

    private $orderingFactory;

    private $view;

    public function __construct(
        DistrictService $districtService,
        DistrictFilterFactory $filterFactory,
        DistrictOrderingFactory $orderingFactory,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->filterFactory = $filterFactory;
        $this->orderingFactory = $orderingFactory;
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
            $this->orderingFactory->createFromRequestInput($orderColumn, $orderDirection),
            $this->filterFactory->createFromRequestInput($filterColumn, $filterValue),
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
