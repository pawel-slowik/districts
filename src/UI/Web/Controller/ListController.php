<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;

final class ListController
{
    private $districtService;

    private $queryFactory;

    private $view;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $query = $this->queryFactory->fromRequest($request, $args);
        $districts = $this->districtService->list($query);
        $queryParams = $request->getQueryParams();
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts,
            "orderColumn" => $args["column"] ?? null,
            "orderDirection" => $args["direction"] ?? null,
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
        ];
        return $this->view->render($response, "list.html", $templateData);
    }
}
