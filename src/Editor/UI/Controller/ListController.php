<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Domain\District;
use Districts\Editor\UI\Factory\ListDistrictsQueryFactory;
use Districts\Editor\UI\Session;
use Districts\Editor\UI\View\ListTemplater;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig as View;

final class ListController
{
    /**
     * @param ListTemplater<District> $listTemplater
     */
    public function __construct(
        private DistrictService $districtService,
        private ListDistrictsQueryFactory $queryFactory,
        private Session $session,
        private ListTemplater $listTemplater,
        private View $view,
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
        $orderingColumns = [
            "city",
            "name",
            "area",
            "population",
        ];
        $templateData = $this->listTemplater->prepareTemplateData(
            $districts,
            $request,
            $orderingColumns,
            "List of districts",
            $this->session->getAndDelete("success.message"),
            $errorMessage ?? null,
        );
        return $this->view->render($response, "list.html", $templateData);
    }
}
