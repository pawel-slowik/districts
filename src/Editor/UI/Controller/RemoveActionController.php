<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Districts\Domain\Exception\DistrictNotFoundException;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\Exception\NotFoundException;
use Districts\Editor\UI\Factory\RemoveDistrictCommandFactory;
use Districts\Editor\UI\ReverseRouter;
use Districts\Editor\UI\Session;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

final class RemoveActionController
{
    public function __construct(
        private DistrictService $districtService,
        private RemoveDistrictCommandFactory $commandFactory,
        private Session $session,
        private ReverseRouter $reverseRouter,
        private ResponseFactory $responseFactory,
    ) {
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = $this->commandFactory->fromRoute($args);
        try {
            $this->districtService->remove($command);
            $this->session->set("success.message", "District data removed.");
        } catch (DistrictNotFoundException | NotFoundException $exception) {
            throw new HttpNotFoundException($request);
        }
        $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
        return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
    }
}
