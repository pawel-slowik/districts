<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\UI\Web\ReverseRouter;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeController
{
    public function __construct(
        private ReverseRouter $reverseRouter,
        private ResponseFactory $responseFactory,
    ) {
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
        return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
    }
}
