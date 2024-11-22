<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Editor\Application\DistrictService;
use Districts\Editor\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\UI\Factory\RemoveDistrictCommandFactory;
use Districts\Editor\UI\Session;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteParserInterface;

final class RemoveActionController
{
    public function __construct(
        private DistrictService $districtService,
        private RemoveDistrictCommandFactory $commandFactory,
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
        $command = $this->commandFactory->fromRoute($args);
        try {
            $this->districtService->remove($command);
            $this->session->set("success.message", "District data removed.");
        } catch (NotFoundInRepositoryException) {
            throw new HttpNotFoundException($request);
        }
        $url = $this->routeParser->relativeUrlFor("list");
        return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
    }
}
