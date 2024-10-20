<?php

declare(strict_types=1);

use Districts\Editor\UI\ErrorHandler\HttpMethodNotAllowedHandler;
use Districts\Editor\UI\ErrorHandler\HttpNotFoundHandler;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return static function (App $app): void {
    $app->add(new Session());
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));
    $errorMiddleware = $app->addErrorMiddleware(false, true, true);
    $errorMiddleware->setErrorHandler(
        HttpNotFoundException::class,
        new HttpNotFoundHandler(),
    );
    $errorMiddleware->setErrorHandler(
        HttpMethodNotAllowedException::class,
        new HttpMethodNotAllowedHandler(),
    );
};
