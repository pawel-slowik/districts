<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\UI\Factory\UpdateDistrictCommandFactory;
use Districts\Editor\UI\Session;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteParserInterface;

final class EditActionController
{
    public function __construct(
        private DistrictService $districtService,
        private UpdateDistrictCommandFactory $commandFactory,
        private Session $session,
        private RouteParserInterface $routeParser,
        private ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @param array<string, string> $args
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = $this->commandFactory->fromRequestAndRoute($request, $args);
            $this->districtService->update($command);
            $this->session->set("success.message", "District data saved successfully.");
            $this->session->delete("form.edit.values");
            $this->session->delete("form.edit.errors");
            $url = $this->routeParser->relativeUrlFor("list");
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        } catch (NotFoundInRepositoryException) {
            throw new HttpNotFoundException($request);
        } catch (ValidationException $validationException) {
            $this->session->set("form.edit.values", $request->getParsedBody());
            $this->session->set("form.edit.error.message", "An error occured while saving district data.");
            $this->session->set("form.edit.errors", array_fill_keys($validationException->getErrors(), true));
            $url = $this->routeParser->relativeUrlFor("edit", ["id" => $args["id"]]);
            return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
        }
    }
}
