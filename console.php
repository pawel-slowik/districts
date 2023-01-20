#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use DI\Container;
use Districts\Scraper\Application\Importer;
use Districts\Scraper\Domain\Gdansk\CityScraper as GdanskScraper;
use Districts\Scraper\Domain\Krakow\CityScraper as KrakowScraper;
use Districts\UI\CLI\ImportCommand;
use Symfony\Component\Console\Application;

$container = new Container();

foreach (["common", "cli"] as $dependencyPart) {
    $dependencies = require __DIR__ . "/dependencies/{$dependencyPart}.php";
    foreach ($dependencies as $dependency => $factory) {
        $container->set($dependency, $factory);
    }
}

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
