<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig as View;
use SlimSession\Helper as Session;

use Service\DistrictService;

final class AddFormController
{
    private $districtService;

    private $session;

    private $view;

    public function __construct(
        DistrictService $districtService,
        Session $session,
        View $view
    ) {
        $this->districtService = $districtService;
        $this->session = $session;
        $this->view = $view;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $cities = $this->districtService->listCities();
        $templateData = [
            "title" => "Add a district",
            "cities" => $cities,
            "district" => $this->session["form.add.values"],
            "errors" => $this->session["form.add.errors"],
        ];
        unset($this->session["form.add.values"]);
        unset($this->session["form.add.errors"]);
        return $this->view->render($response, "add.html", $templateData);
    }
}
