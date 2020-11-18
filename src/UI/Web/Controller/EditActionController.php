<?php

declare(strict_types=1);

namespace UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

use UI\Web\Redirector;
use UI\Web\RequestParser;

use Service\DistrictService;
use Service\NotFoundException;
use Service\ValidationException;

final class EditActionController
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

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parsed = $request->getParsedBody();
        try {
            $command = $this->requestParser->parseUpdate($request, $args);
            $this->districtService->update($command);
            // TODO: flash success message
            unset($this->session["form.edit.values"]);
            unset($this->session["form.edit.errors"]);
            return $this->redirector->redirect($request, $response, "list");
        } catch (NotFoundException $notFoundException) {
            throw new HttpNotFoundException($request);
        } catch (ValidationException $validationException) {
            // TODO: flash error message
            $this->session["form.edit.values"] = $parsed;
            $this->session["form.edit.errors"] = array_fill_keys($validationException->getErrors(), true);
            return $this->redirector->redirect($request, $response, "edit", ["id" => $args["id"]]);
        }
    }
}
