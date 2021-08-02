<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use DI\Container;
use Districts\UI\Web\RoutingConfiguration;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;

$container = new Container();

foreach (["common", "web"] as $dependencyPart) {
    $dependencies = require __DIR__ . "/../dependencies/{$dependencyPart}.php";
    $dependencies($container);
}

$app = new App(
    $container->get(ResponseFactoryInterface::class),
    $container,
    $container->get(CallableResolverInterface::class),
    $container->get(RouteCollectorInterface::class)
);

$middleware = require __DIR__ . "/../src/middleware.php";
$middleware($app);

$app = RoutingConfiguration::apply($app);

$app->run();
