<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use DI\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App;
use Districts\UI\Web\RoutingConfiguration;

$container = new Container();
$app = new App(new Psr17Factory(), $container);

$dependencies = require __DIR__ . "/../src/dependencies.php";
$dependencies($container, $app);

$middleware = require __DIR__ . "/../src/middleware.php";
$middleware($app);

$app = RoutingConfiguration::apply($app);

$app->run();
