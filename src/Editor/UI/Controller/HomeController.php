<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Controller;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteParserInterface;

final readonly class HomeController
{
    public function __construct(
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
        $url = $this->routeParser->relativeUrlFor("list");
        return $this->responseFactory->createResponse(StatusCode::STATUS_FOUND)->withHeader("Location", $url);
    }
}
