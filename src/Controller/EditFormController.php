<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EditFormController extends BaseCrudController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $district = $this->repository->get(intval($args["id"]));
        if (!$district) {
            throw new NotFoundException($request, $response);
        }
        $templateData = [
            "title" => "Edit a district",
            "district" => $district,
        ];
        return $this->view->render($response, "edit.html", $templateData);
    }
}
