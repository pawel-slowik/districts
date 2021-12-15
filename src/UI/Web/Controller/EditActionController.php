<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\NotFoundException;
use Districts\Application\Exception\ValidationException;
use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\UI\Web\Factory\UpdateDistrictCommandFactory;
use Districts\UI\Web\ReverseRouter;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

final class EditActionController
{
    private DistrictService $districtService;

    private UpdateDistrictCommandFactory $commandFactory;

    private Session $session;

    private ReverseRouter $reverseRouter;

    public function __construct(
        DistrictService $districtService,
        UpdateDistrictCommandFactory $commandFactory,
        Session $session,
        ReverseRouter $reverseRouter
    ) {
        $this->districtService = $districtService;
        $this->commandFactory = $commandFactory;
        $this->session = $session;
        $this->reverseRouter = $reverseRouter;
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
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
            return (new NyholmResponse())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
        } catch (NotFoundException | DistrictNotFoundException $notFoundException) {
            throw new HttpNotFoundException($request);
        } catch (ValidationException $validationException) {
            $this->session["form.edit.values"] = $request->getParsedBody();
            $this->session["form.edit.error.message"] = "An error occured while saving district data.";
            $this->session["form.edit.errors"] = array_fill_keys($validationException->getErrors(), true);
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "edit", ["id" => $args["id"]]);
            return (new NyholmResponse())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
        }
    }
}
