<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use Districts\DependencyContainerFactory;
use Districts\Editor\UI\Middleware;
use Districts\Editor\UI\RoutingConfiguration;
use Slim\App;
use Slim\Views\Twig;

$container = DependencyContainerFactory::create(["common", "editor"]);

/** @var Twig $twig */
$twig = $container->get(Twig::class);
/** @var App<null> $app */
$app = $container->get(App::class);

Middleware::setUp($app, $twig);
RoutingConfiguration::apply($app);

$app->run();
