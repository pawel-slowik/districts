<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Psr\Container\ContainerInterface;
use Slim\App;
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
        $app->addErrorMiddleware(false, true, true);
    }
}
