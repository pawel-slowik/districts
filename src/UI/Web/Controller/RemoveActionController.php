<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

use UI\Web\Redirector;
use UI\Web\RequestParser;

use Service\DistrictService;
use Service\NotFoundException;

final class RemoveActionController
{
    private $districtService;

    private $requestParser;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        RequestParser $requestParser,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->requestParser = $requestParser;
        $this->redirector = $redirector;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $this->districtService->remove($this->requestParser->parseRemove($request, $args));
            // TODO: flash message
        } catch (NotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        return $this->redirector->redirect($request, $response, "list");
    }
}
