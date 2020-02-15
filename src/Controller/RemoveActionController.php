<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

use Repository\DistrictRepository;

final class RemoveActionController
{
    private $districtRepository;

    private $redirector;

    public function __construct(
        DistrictRepository $districtRepository,
        Redirector $redirector
    ) {
        $this->districtRepository = $districtRepository;
        $this->redirector = $redirector;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $district = $this->districtRepository->get(intval($args["id"]));
        if (!$district) {
            throw new HttpNotFoundException($request);
        }
        if ($this->isConfirmed($request)) {
            $this->districtRepository->remove($district);
            // TODO: flash message
        }
        return $this->redirectToListResponse($request, $response);
    }

    private function isConfirmed(Request $request): bool
    {
        $parsed = $request->getParsedBody();
        return array_key_exists("confirm", $parsed);
    }

    private function redirectToListResponse(Request $request, Response $response): Response
    {
        return $this->redirector->redirect($request, $response, "list");
    }
}
