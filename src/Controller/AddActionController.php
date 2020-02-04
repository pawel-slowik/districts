<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Entity\District;
use Validator\NewDistrictValidator;

final class AddActionController extends BaseCrudController
{
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
}
