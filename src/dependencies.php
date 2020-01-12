<?php

declare(strict_types=1);

use Slim\App;
use Slim\Views\Twig;

use Repository\DistrictRepository;
use Controller\ListController;
use Controller\AddFormController;
use Controller\AddActionController;
use Controller\EditFormController;
use Controller\EditActionController;
use Controller\RemoveFormController;
use Controller\RemoveActionController;

return function (App $app): void {
    $container = $app->getContainer();

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $container->set("view", function ($container) {
        $options = [
            "cache" => "/tmp/twig_cache",
            "auto_reload" => true,
        ];
        $view = Twig::create("templates", $options);
        return $view;
    });

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $container->set("route_parser", function ($container) use ($app) {
        return $app->getRouteCollector()->getRouteParser();
    });

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $container->set("session", function ($container) {
        return new \SlimSession\Helper();
    });

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $container->set(DistrictRepository::class, function ($container) {
        $entityManagerFactory = require "doctrine-bootstrap.php";
        $entityManager = $entityManagerFactory();
        $repository = new DistrictRepository($entityManager);
        return $repository;
    });

    // all the basic controllers have the same dependencies
    $basicControllerClasses = [
        ListController::class,
        AddFormController::class,
        AddActionController::class,
        EditFormController::class,
        EditActionController::class,
        RemoveFormController::class,
        RemoveActionController::class,
    ];
    foreach ($basicControllerClasses as $controllerClass) {
        $container->set($controllerClass, function ($container) use ($controllerClass) {
            return new $controllerClass(
                $container->get(DistrictRepository::class),
                $container->get("session"),
                $container->get("route_parser"),
                $container->get("view")
            );
        });
    }
};
