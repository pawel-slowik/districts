<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use DI\ContainerBuilder;
use Districts\Editor\UI\Middleware;
use Districts\Editor\UI\RoutingConfiguration;
use Slim\App;
use Slim\Views\Twig;

$container = (new ContainerBuilder())
    ->addDefinitions(__DIR__ . "/../src/Core/config.php")
    ->addDefinitions(__DIR__ . "/../src/Editor/config.php")
    ->build();

/** @var Twig $twig */
$twig = $container->get(Twig::class);
/** @var App<null> $app */
$app = $container->get(App::class);

Middleware::setUp($app, $twig);
RoutingConfiguration::apply($app);

$app->run();
