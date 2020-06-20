<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

use UI\Web\Redirector;

use Service\DistrictService;
use Service\NotFoundException;

final class RemoveActionController
{
    private $districtService;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->redirector = $redirector;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if ($this->isConfirmed($request)) {
            try {
                $this->districtService->remove($args["id"]);
                // TODO: flash message
            } catch (NotFoundException $exception) {
                throw new HttpNotFoundException($request);
            }
        }
        return $this->redirector->redirect($request, $response, "list");
    }

    private function isConfirmed(Request $request): bool
    {
        $parsed = $request->getParsedBody();
        return array_key_exists("confirm", $parsed);
    }
}
