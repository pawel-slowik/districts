<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Slim\App;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

final class Middleware
{
    /**
     * @param App<null> $app
     */
    public static function setUp(App $app, Twig $twig): void
    {
        $app->add(new Session());
        $app->add(TwigMiddleware::create($app, $twig));
        $app->addErrorMiddleware(false, true, true);
    }
}
