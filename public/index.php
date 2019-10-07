<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$app = new \Slim\App();
$app->add(new \Slim\Middleware\Session());

$dependencies = require __DIR__ . "/../src/dependencies.php";
$dependencies($app);

$routes = require __DIR__ . "/../src/routes.php";
$routes($app);

$app->run();
