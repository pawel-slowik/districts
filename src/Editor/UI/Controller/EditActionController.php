<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\Exception\NotFoundException;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Domain\Exception\DistrictNotFoundException;
use Districts\Editor\UI\Factory\UpdateDistrictCommandFactory;
use Districts\Editor\UI\ReverseRouter;
use Districts\Editor\UI\Session;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

final class EditActionController
{
    public function __construct(
        private DistrictService $districtService,
        private UpdateDistrictCommandFactory $commandFactory,
        private Session $session,
        private ReverseRouter $reverseRouter,
        private ResponseFactory $responseFactory,
    ) {
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = $this->commandFactory->fromRequest($request, $args);
            $this->districtService->update($command);
            $this->session->set("success.message", "District data saved successfully.");
            $this->session->delete("form.edit.values");
            $this->session->delete("form.edit.errors");
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        } catch (NotFoundException | DistrictNotFoundException $notFoundException) {
            throw new HttpNotFoundException($request);
        } catch (ValidationException $validationException) {
            $this->session->set("form.edit.values", $request->getParsedBody());
            $this->session->set("form.edit.error.message", "An error occured while saving district data.");
            $this->session->set("form.edit.errors", array_fill_keys($validationException->getErrors(), true));
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "edit", ["id" => $args["id"]]);
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        }
    }
}
