<?php

declare(strict_types=1);

use Slim\App;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use ErrorHandlers\HttpNotFoundHandler;
use ErrorHandlers\HttpMethodNotAllowedHandler;

return function (App $app): void {
    $app->add(new Session());
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));
    $errorMiddleware = $app->addErrorMiddleware(false, true, true);
    $errorMiddleware->setErrorHandler(
        HttpNotFoundException::class,
        HttpNotFoundHandler::class
    );
    $errorMiddleware->setErrorHandler(
        HttpMethodNotAllowedException::class,
        HttpMethodNotAllowedHandler::class
    );
};
