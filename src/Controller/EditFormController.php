<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EditFormController extends BaseCrudController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $district = $this->session["form.edit.values"];
        if (!$district) {
            $district = $this->districtRepository->get(intval($args["id"]));
        }
        if (!$district) {
            throw new NotFoundException($request, $response);
        }
        $templateData = [
            "title" => "Edit a district",
            "district" => $district,
            "errors" => $this->session["form.edit.errors"],
        ];
        unset($this->session["form.edit.values"]);
        unset($this->session["form.edit.errors"]);
        return $this->view->render($response, "edit.html", $templateData);
    }
}
