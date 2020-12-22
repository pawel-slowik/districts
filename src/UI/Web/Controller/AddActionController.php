<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSession\Helper as Session;

use Districts\UI\Web\Redirector;
use Districts\UI\Web\Factory\AddDistrictCommandFactory;

use Districts\Application\DistrictService;
use Districts\Service\ValidationException;

final class AddActionController
{
    private $districtService;

    private $commandFactory;

    private $session;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        AddDistrictCommandFactory $commandFactory,
        Session $session,
        Redirector $redirector
    ) {
        $this->districtService = $districtService;
        $this->commandFactory = $commandFactory;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parsed = $request->getParsedBody();
        try {
            $command = $this->commandFactory->fromRequest($request);
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
