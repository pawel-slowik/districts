<?php

declare(strict_types=1);

namespace ErrorHandlers;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Response;

class HttpNotFoundHandler
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(Request $request, \Throwable $exception, bool $displayErrorDetails): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(StatusCode::STATUS_NOT_FOUND . ' NOT FOUND');
        return $response->withStatus(StatusCode::STATUS_NOT_FOUND);
    }
}
