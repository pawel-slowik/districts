<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Http\StatusCode;

use Entity\District;
use Validator\DistrictValidator;

class EditActionController extends BaseCrudController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $district = $this->repository->get(intval($args["id"]));
        if (!$district) {
            throw new NotFoundException($request, $response);
        }
        $validator = new DistrictValidator();
        $parsed = $request->getParsedBody();
        $validationResult = $validator->validate($parsed);
        if (!$validationResult->isOk()) {
            // TODO: flash error message
            $this->session["form.edit.values"] = $parsed;
            $this->session["form.edit.errors"] = array_fill_keys($validationResult->getErrors(), true);
            return $this->redirectToEditResponse($response, $district);
        }
        $validated = $validationResult->getValidatedData();
        $district->setName($validated["name"]);
        $district->setArea($validated["area"]);
        $district->setPopulation($validated["population"]);
        $this->repository->update($district);
        // TODO: flash success message
        unset($this->session["form.edit.values"]);
        unset($this->session["form.edit.errors"]);
        return $this->redirectToListResponse($response);
    }

    protected function redirectToEditResponse(Response $response, District $district)
    {
        $url = $this->router->pathFor("edit", ["id" => $district->getId()]);
        return $response->withHeader("Location", $url)->withStatus(StatusCode::HTTP_FOUND);
    }
}
