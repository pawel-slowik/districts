<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Slim\Exception\NotFoundException;

final class RemoveActionController extends BaseCrudController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $district = $this->districtRepository->get(intval($args["id"]));
        if (!$district) {
            throw new NotFoundException($request, $response);
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
}
