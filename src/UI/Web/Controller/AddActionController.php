<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSession\Helper as Session;

use UI\Web\Redirector;
use UI\Web\RequestParser;

use Service\DistrictService;
use Service\ValidationException;

final class AddActionController
{
    private $districtService;

    private $requestParser;

    private $session;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        RequestParser $requestParser,
        Session $session,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->requestParser = $requestParser;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parsed = $request->getParsedBody();
        try {
            $command = $this->requestParser->parseAdd($request);
            $this->districtService->add($command);
            // TODO: flash success message
            unset($this->session["form.add.values"]);
            unset($this->session["form.add.errors"]);
            return $this->redirector->redirect($request, $response, "list");
        } catch (ValidationException $exception) {
            // TODO: flash error message
            $this->session["form.add.values"] = $parsed;
            $this->session["form.add.errors"] = array_fill_keys($exception->getErrors(), true);
            return $this->redirector->redirect($request, $response, "add");
        }
    }
}
