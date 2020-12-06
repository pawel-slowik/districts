<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;

use Districts\UI\Web\Factory\GetDistrictQueryFactory;
use Districts\Service\DistrictService;
use Districts\Service\NotFoundException;

final class RemoveFormController
{
    private $districtService;

    private $queryFactory;

    private $view;

    public function __construct(
        DistrictService $districtService,
        GetDistrictQueryFactory $queryFactory,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->queryFactory = $queryFactory;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $district = $this->districtService->get($this->queryFactory->fromRequest($request, $args));
        } catch (NotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        $templateData = [
            "title" => "Remove a district",
            "district" => $district,
        ];
        return $this->view->render($response, "remove.html", $templateData);
    }
}
