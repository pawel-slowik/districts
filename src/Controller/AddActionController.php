<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouteParserInterface;
use SlimSession\Helper as Session;

use Entity\District;
use Validator\NewDistrictValidator;
use Repository\DistrictRepository;
use Repository\CityRepository;

final class AddActionController
{
    private $cityRepository;

    private $districtRepository;

    private $session;

    private $routeParser;

    public function __construct(
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        Session $session,
        RouteParserInterface $routeParser
    ) {
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->session = $session;
        $this->routeParser = $routeParser;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $validCityIds = array_map(
            function ($city) {
                return $city->getId();
            },
            $this->cityRepository->list()
        );
        $validator = new NewDistrictValidator($validCityIds);
        $parsed = $request->getParsedBody();
        $validationResult = $validator->validate($parsed);
        if (!$validationResult->isOk()) {
            // TODO: flash error message
            $this->session["form.add.values"] = $parsed;
            $this->session["form.add.errors"] = array_fill_keys($validationResult->getErrors(), true);
            return $this->redirectToAddResponse($request, $response);
        }
        $validated = $validationResult->getValidatedData();
        $city = $this->cityRepository->get($validated["city"]);
        $district = new District($validated["name"], $validated["area"], $validated["population"]);
        $district->setCity($city);
        $this->districtRepository->add($district);
        // TODO: flash success message
        unset($this->session["form.add.values"]);
        unset($this->session["form.add.errors"]);
        return $this->redirectToListResponse($request, $response);
    }

    private function redirectToAddResponse(Request $request, Response $response): Response
    {
        $url = $this->routeParser->fullUrlFor($request->getUri(), "add");
        return $response->withHeader("Location", $url)->withStatus(302);
    }

    private function redirectToListResponse(Request $request, Response $response): Response
    {
        $url = $this->routeParser->fullUrlFor($request->getUri(), "list");
        return $response->withHeader("Location", $url)->withStatus(302);
    }
}
