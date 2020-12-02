#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Districts\UI\CLI\UpdateCommand;
use Districts\Validator\DistrictValidator;
use Districts\Repository\CityRepository;
use Districts\Repository\DistrictRepository;
use Districts\Service\Importer;
use Districts\Service\CityIterator;
use Districts\Scraper\GuzzleHtmlFetcher;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$cityRepository = new CityRepository($entityManager);
$application = new Application();
$application->add(new UpdateCommand(
    new Importer(
        new DistrictValidator(new CityIterator($cityRepository)),
        new DistrictRepository($entityManager),
        $cityRepository,
    ),
    new GuzzleHtmlFetcher(),
));
$application->run();
