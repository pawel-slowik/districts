<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use Districts\DependencyContainerFactory;
use Districts\Editor\UI\Middleware;
use Districts\Editor\UI\RoutingConfiguration;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;

$container = DependencyContainerFactory::create(["common", "editor"]);

$app = new App(
    $container->get(ResponseFactoryInterface::class),
    $container,
    $container->get(CallableResolverInterface::class),
    $container->get(RouteCollectorInterface::class)
);

Middleware::setUp($app);
RoutingConfiguration::apply($app);

$app->run();
