<?php

declare(strict_types=1);

use Slim\App;
use Slim\Views\Twig;
use Slim\Interfaces\RouteParserInterface;

use Repository\DistrictRepository;

return function (App $app): void {
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
        DistrictRepository::class => function ($container) {
            $entityManagerFactory = require "doctrine-bootstrap.php";
            $entityManager = $entityManagerFactory();
            return new DistrictRepository($entityManager);
        },

    ];

    foreach ($dependencies as $dependency => $factory) {
        $app->getContainer()->set($dependency, $factory);
    }
};
