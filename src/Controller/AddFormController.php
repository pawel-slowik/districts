<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AddFormController extends BaseCrudController
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args)
    {
        $cities = $this->repository->listCities();
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
