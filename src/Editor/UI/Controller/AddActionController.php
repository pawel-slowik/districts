<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\AddDistrictCommandFactory;
use Districts\Editor\UI\ReverseRouter;
use Districts\Editor\UI\Session;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AddActionController
{
    public function __construct(
        private DistrictService $districtService,
        private AddDistrictCommandFactory $commandFactory,
        private Session $session,
        private ReverseRouter $reverseRouter,
        private ResponseFactory $responseFactory,
    ) {
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = $this->commandFactory->fromRequest($request);
            $this->districtService->add($command);
            $this->session->set("success.message", "District data saved successfully.");
            $this->session->delete("form.add.values");
            $this->session->delete("form.add.errors");
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        } catch (ValidationException $exception) {
            $this->session->set("form.add.values", $request->getParsedBody());
            $this->session->set("form.add.error.message", "An error occured while saving district data.");
            $this->session->set("form.add.errors", array_fill_keys($exception->getErrors(), true));
            $url = $this->reverseRouter->urlFromRoute($request->getUri(), "add");
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        }
    }
}
