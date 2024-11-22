<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Districts\Editor\UI\ErrorHandler\HttpMethodNotAllowedHandler;
use Districts\Editor\UI\ErrorHandler\HttpNotFoundHandler;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

class Middleware
{
    /**
     * @param App<ContainerInterface> $app
     */
    public static function setUp(App $app): void
    {
        $app->add(new Session());
        $app->add(TwigMiddleware::createFromContainer($app, Twig::class));
        $errorMiddleware = $app->addErrorMiddleware(false, true, true);
        $errorMiddleware->setErrorHandler(HttpNotFoundException::class, new HttpNotFoundHandler());
        $errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, new HttpMethodNotAllowedHandler());
    }
}
