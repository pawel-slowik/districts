<?php

declare(strict_types=1);

use DI\Container;
use Slim\App;
use Slim\Views\Twig;
use Slim\Interfaces\RouteParserInterface;

use Doctrine\ORM\EntityManager;

return function (Container $container, App $app): void {
    $dependencies = [

        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        Twig::class => function ($container) {
            return Twig::create(
                "templates",
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

        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        EntityManager::class => function ($container) {
            return (require "doctrine-bootstrap.php")();
        },

    ];

    foreach ($dependencies as $dependency => $factory) {
        $container->set($dependency, $factory);
    }
};
