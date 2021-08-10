<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\NotFoundException;
use Districts\Application\Exception\ValidationException;
use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\UI\Web\Factory\UpdateDistrictCommandFactory;
use Districts\UI\Web\Redirector;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

final class EditActionController
{
    private $districtService;

    private $commandFactory;

    private $session;

    private $redirector;

    public function __construct(
        DistrictService $districtService,
        UpdateDistrictCommandFactory $commandFactory,
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
            $command = $this->commandFactory->fromRequest($request, $args);
            $this->districtService->update($command);
            $this->session["success.message"] = "District data saved successfully.";
            unset($this->session["form.edit.values"]);
            unset($this->session["form.edit.errors"]);
            return $this->redirector->redirect($request->getUri(), "list");
        } catch (NotFoundException | DistrictNotFoundException $notFoundException) {
            throw new HttpNotFoundException($request);
        } catch (ValidationException $validationException) {
            $this->session["form.edit.values"] = $request->getParsedBody();
            $this->session["form.edit.error.message"] = "An error occured while saving district data.";
            $this->session["form.edit.errors"] = array_fill_keys($validationException->getErrors(), true);
            return $this->redirector->redirect($request->getUri(), "edit", ["id" => $args["id"]]);
        }
    }
}
