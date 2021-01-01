<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

use Districts\UI\Web\Redirector;
use Districts\UI\Web\Factory\UpdateDistrictCommandFactory;

use Districts\Application\DistrictService;
use Districts\Application\NotFoundException as ApplicationNotFoundException;
use Districts\Application\ValidationException as RequestValidationException;
use Districts\DomainModel\NotFoundException as DomainNotFoundException;
use Districts\DomainModel\ValidationException as DomainValidationException;

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

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $parsed = $request->getParsedBody();
        try {
            $command = $this->commandFactory->fromRequest($request, $args);
            $this->districtService->update($command);
            $this->session["success.message"] = "District data saved successfully.";
            unset($this->session["form.edit.values"]);
            unset($this->session["form.edit.errors"]);
            return $this->redirector->redirect($request, $response, "list");
        } catch (ApplicationNotFoundException | DomainNotFoundException $notFoundException) {
            throw new HttpNotFoundException($request);
        } catch (RequestValidationException | DomainValidationException $validationException) {
            $this->session["form.edit.values"] = $parsed;
            $this->session["form.edit.error.message"] = "An error occured while saving district data.";
            $this->session["form.edit.errors"] = array_fill_keys($validationException->getErrors(), true);
            return $this->redirector->redirect($request, $response, "edit", ["id" => $args["id"]]);
        }
    }
}
