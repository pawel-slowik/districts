<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\ValidationException;
use Districts\UI\Web\Factory\AddDistrictCommandFactory;
use Districts\UI\Web\Redirector;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimSession\Helper as Session;

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
        try {
            $command = $this->commandFactory->fromRequest($request);
            $this->districtService->add($command);
            $this->session["success.message"] = "District data saved successfully.";
            unset($this->session["form.add.values"]);
            unset($this->session["form.add.errors"]);
            return $this->redirector->redirect($request->getUri(), "list");
        } catch (ValidationException $exception) {
            $this->session["form.add.values"] = $request->getParsedBody();
            $this->session["form.add.error.message"] = "An error occured while saving district data.";
            $this->session["form.add.errors"] = array_fill_keys($exception->getErrors(), true);
            return $this->redirector->redirect($request->getUri(), "add");
        }
    }
}
