<?php

declare(strict_types=1);

use Slim\App;

use Repository\DistrictRepository;
use Controller\ListController;
use Controller\AddController;
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

    $container[ListController::class] = function ($container) {
        return new ListController($container->get(DistrictRepository::class), $container->get("view"));
    };

    $container[AddController::class] = function ($container) {
        return new AddController($container->get(DistrictRepository::class), $container->get("view"));
    };

    $container[EditController::class] = function ($container) {
        return new EditController($container->get(DistrictRepository::class), $container->get("view"));
    };

    $container[RemoveFormController::class] = function ($container) {
        return new RemoveFormController(
            $container->get(DistrictRepository::class),
            $container->get("view")
        );
    };

    $container[RemoveActionController::class] = function ($container) {
        return new RemoveActionController(
            $container->get(DistrictRepository::class),
            $container->get("router"),
            $container->get("view")
        );
    };
};
