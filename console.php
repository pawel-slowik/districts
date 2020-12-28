#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Districts\UI\CLI\UpdateCommand;
use Districts\Infrastructure\CityRepository;
use Districts\Application\Importer;
use Districts\Scraper\GuzzleHtmlFetcher;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$application = new Application();
$application->add(new UpdateCommand(
    new Importer(new CityRepository($entityManager)),
    new GuzzleHtmlFetcher(),
));
$application->run();
