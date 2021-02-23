<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

use Districts\Application\DistrictService;
use Districts\UI\Web\Factory\ListDistrictsQueryFactory;
use Districts\UI\Web\Factory\RoutedPageReferenceFactory;

final class ListController
{
    private $districtService;

    private $queryFactory;

    private $session;

    private $view;

    private $pageReferenceFactory;

    public function __construct(
        DistrictService $districtService,
        ListDistrictsQueryFactory $queryFactory,
        Session $session,
        View $view,
        RoutedPageReferenceFactory $pageReferenceFactory
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->session = $session;
        $this->view = $view;
        $this->pageReferenceFactory = $pageReferenceFactory;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $query = $this->queryFactory->fromRequest($request, $args);
        $districts = $this->districtService->list($query);
        $queryParams = $request->getQueryParams();
        $pageCount = $districts->getPageCount();
        $currentPageNumber = is_null($query->getPagination()) ? 1 : $query->getPagination()->getPageNumber();
        $templateData = [
            "title" => "List of districts",
            "districts" => $districts,
            "orderColumn" => $args["column"] ?? null,
            "orderDirection" => $args["direction"] ?? null,
            "filterColumn" => $queryParams["filterColumn"] ?? null,
            "filterValue" => $queryParams["filterValue"] ?? null,
            "pagination" => iterator_to_array(
                $this->pageReferenceFactory->createPageReferences(
                    $request,
                    $pageCount,
                    $currentPageNumber,
                )
            ),
            "successMessage" => $this->session["success.message"],
        ];
        unset($this->session["success.message"]);
        return $this->view->render($response, "list.html", $templateData);
    }
}
