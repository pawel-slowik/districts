#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Districts\UI\CLI\UpdateCommand;
use Districts\Repository\CityRepository;
use Districts\Service\Importer;
use Districts\Scraper\GuzzleHtmlFetcher;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$cityRepository = new CityRepository($entityManager);
$application = new Application();
$application->add(new UpdateCommand(
    new Importer(
        $cityRepository,
    ),
    new GuzzleHtmlFetcher(),
));
$application->run();
