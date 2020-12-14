#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Districts\UI\CLI\UpdateCommand;
use Districts\Validator\DistrictValidator;
use Districts\Repository\CityRepository;
use Districts\Repository\DistrictRepository;
use Districts\Service\DistrictService;
use Districts\Service\Importer;
use Districts\Scraper\GuzzleHtmlFetcher;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$cityRepository = new CityRepository($entityManager);
$districtRepository = new DistrictRepository($entityManager);
$districtValidator = new DistrictValidator();
$application = new Application();
$application->add(new UpdateCommand(
    new Importer(
        new DistrictService(
            $districtRepository,
            $districtValidator,
            $cityRepository,
        ),
        $cityRepository,
    ),
    new GuzzleHtmlFetcher(),
));
$application->run();
