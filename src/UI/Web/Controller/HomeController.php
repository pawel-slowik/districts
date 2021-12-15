<?php

declare(strict_types=1);

namespace Districts\UI\Web\Controller;

use Districts\UI\Web\ReverseRouter;
use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeController
{
    private ReverseRouter $reverseRouter;

    public function __construct(ReverseRouter $reverseRouter)
    {
        $this->reverseRouter = $reverseRouter;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $url = $this->reverseRouter->urlFromRoute($request->getUri(), "list");
        return (new NyholmResponse())->withHeader("Location", $url)->withStatus(StatusCode::STATUS_FOUND);
    }
}
