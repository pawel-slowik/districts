<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig as View;

use Service\DistrictService;
use Service\NotFoundException;

final class RemoveFormController
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

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $district = $this->districtService->get($args["id"]);
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
