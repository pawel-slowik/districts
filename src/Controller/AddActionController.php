<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Http\StatusCode;

use Entity\District;
use Validator\NewDistrictValidator;

class AddActionController extends BaseCrudController
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args)
    {
        $validCityIds = array_map(
            function ($city) {
                return $city->getId();
            },
            $this->repository->listCities()
        );
        $validator = new NewDistrictValidator($validCityIds);
        $validationResult = $validator->validate($request->getParsedBody());
        if (!$validationResult->isOk()) {
            // TODO: flash error message
            // TODO: mark form fields as invalid
            // TODO: save filled in values in session
            return $this->redirectToAddResponse($response);
        }
        $validated = $validationResult->getValidatedData();
        $city = $this->repository->getCity($validated["city"]);
        $district = new District($validated["name"], $validated["area"], $validated["population"]);
        $this->repository->add($city, $district);
        // TODO: flash success message
        // TODO: clear invalid field markings
        // TODO: clear saved values from session
        return $this->redirectToListResponse($response);
    }

    protected function redirectToAddResponse(Response $response)
    {
        $url = $this->router->pathFor("add");
        return $response->withHeader("Location", $url)->withStatus(StatusCode::HTTP_FOUND);
    }
}
