<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteParserInterface;
use SlimSession\Helper as Session;

use Entity\District;
use Validator\DistrictValidator;
use Repository\DistrictRepository;

final class EditActionController
{
    private $districtRepository;

    private $session;

    private $routeParser;

    public function __construct(
        DistrictRepository $districtRepository,
        Session $session,
        RouteParserInterface $routeParser
    ) {
        $this->districtRepository = $districtRepository;
        $this->session = $session;
        $this->routeParser = $routeParser;
    }

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

    private function redirectToListResponse(Request $request, Response $response): Response
    {
        $url = $this->routeParser->fullUrlFor($request->getUri(), "list");
        return $response->withHeader("Location", $url)->withStatus(302);
    }
}
