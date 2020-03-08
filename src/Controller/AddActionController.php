<?php

declare(strict_types=1);

namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSession\Helper as Session;

use Service\DistrictService;
use Service\ValidationException;

final class AddActionController
{
    private $districtService;

    private $session;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        Session $session,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parsed = $request->getParsedBody();
        try {
            $this->districtService->add(
                $parsed["name"] ?? null,
                $parsed["area"] ?? null,
                $parsed["population"] ?? null,
                $parsed["city"] ?? null
            );
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
