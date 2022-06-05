<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\Application\DistrictService;
use Districts\Application\Exception\NotFoundException;
use Districts\DomainModel\Exception\DistrictNotFoundException;
use Districts\UI\Web\Factory\RemoveDistrictCommandFactory;
use Districts\UI\Web\ReverseRouter;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as Session;

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
