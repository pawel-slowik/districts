<?php

declare(strict_types=1);

namespace Districts\Editor\UI\ErrorHandler;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class HttpNotFoundHandler implements ErrorHandlerInterface
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function __invoke(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $response = new Response();
        $response->getBody()->write(StatusCode::STATUS_NOT_FOUND . ' NOT FOUND');
        return $response->withStatus(StatusCode::STATUS_NOT_FOUND);
    }
}
