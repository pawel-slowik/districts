<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Exception\HttpNotFoundException;
use Entity\District;
use Validator\DistrictValidator;

final class EditActionController extends BaseCrudController
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $district = $this->districtRepository->get(intval($args["id"]));
        if (!$district) {
            throw new HttpNotFoundException($request);
        }
        $validator = new DistrictValidator();
        $parsed = $request->getParsedBody();
        $validationResult = $validator->validate($parsed);
        if (!$validationResult->isOk()) {
            // TODO: flash error message
            $this->session["form.edit.values"] = $parsed;
            $this->session["form.edit.errors"] = array_fill_keys($validationResult->getErrors(), true);
            return $this->redirectToEditResponse($request, $response, $district);
        }
        $validated = $validationResult->getValidatedData();
        $district->setName($validated["name"]);
        $district->setArea($validated["area"]);
        $district->setPopulation($validated["population"]);
        $this->districtRepository->update($district);
        // TODO: flash success message
        unset($this->session["form.edit.values"]);
        unset($this->session["form.edit.errors"]);
        return $this->redirectToListResponse($request, $response);
    }

    private function redirectToEditResponse(Request $request, Response $response, District $district): Response
    {
        $url = $this->routeParser->fullUrlFor($request->getUri(), "edit", ["id" => $district->getId()]);
        return $response->withHeader("Location", $url)->withStatus(302);
    }
}
