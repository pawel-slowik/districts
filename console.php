#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use DI\Container;
use Districts\Application\Importer;
use Districts\DomainModel\Scraper\Gdansk\CityScraper as GdanskScraper;
use Districts\DomainModel\Scraper\Krakow\CityScraper as KrakowScraper;
use Districts\UI\CLI\ImportCommand;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App;
use Symfony\Component\Console\Application;

$container = new Container();
$app = new App(new Psr17Factory(), $container);

$dependencies = require __DIR__ . "/src/dependencies.php";
$dependencies($container, $app);

$application = new Application();
$application->add(
    new ImportCommand(
        $container->get(Importer::class),
        [
            $container->get(GdanskScraper::class),
            $container->get(KrakowScraper::class),
        ]
    )
);
$application->run();
