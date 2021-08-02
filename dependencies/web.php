<?php

declare(strict_types=1);

use DI\Container;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;

return function (Container $container, App $app): void {
    $dependencies = [

        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        Twig::class => function ($container) {
            return Twig::create(
                __DIR__ . "/../templates",
                [
                    "cache" => "/tmp/twig_cache",
                    "auto_reload" => true,
                ]
            );
        },

        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        RouteParserInterface::class => function ($container) use ($app) {
            return $app->getRouteCollector()->getRouteParser();
        },

    ];

    foreach ($dependencies as $dependency => $factory) {
        $container->set($dependency, $factory);
    }
};
