<?php

declare(strict_types=1);

use Slim\App;

use Repository\DistrictRepository;
use Controller\ListController;
use Controller\AddFormController;
use Controller\AddActionController;
use Controller\EditController;
use Controller\RemoveFormController;
use Controller\RemoveActionController;

return function (App $app): void {
    $container = $app->getContainer();

    $container["view"] = function ($container) {
        $options = [
            "cache" => "/tmp/twig_cache",
            "auto_reload" => true,
        ];
        $view = new \Slim\Views\Twig("templates", $options);
        $router = $container->get("router");
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        return $view;
    };

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    $container[DistrictRepository::class] = function ($container) {
        $entityManagerFactory = require "doctrine-bootstrap.php";
        $entityManager = $entityManagerFactory();
        $repository = new DistrictRepository($entityManager);
        return $repository;
    };

    // all the basic controllers have the same dependencies
    $basicControllerClasses = [
        ListController::class,
        AddFormController::class,
        AddActionController::class,
        EditController::class,
        RemoveFormController::class,
        RemoveActionController::class,
    ];
    foreach ($basicControllerClasses as $controllerClass) {
        $container[$controllerClass] = function ($container) use ($controllerClass) {
            return new $controllerClass(
                $container->get(DistrictRepository::class),
                $container->get("router"),
                $container->get("view")
            );
        };
    }
};
