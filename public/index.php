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

/** @var ResponseFactoryInterface */
$responseFactory = $container->get(ResponseFactoryInterface::class);
/** @var CallableResolverInterface */
$callableResolver = $container->get(CallableResolverInterface::class);
/** @var RouteCollectorInterface */
$routeCollector = $container->get(RouteCollectorInterface::class);
$app = new App($responseFactory, $container, $callableResolver, $routeCollector);

Middleware::setUp($app);
RoutingConfiguration::apply($app);

$app->run();
