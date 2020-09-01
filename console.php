#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use UI\CLI\UpdateCommand;
use Validator\DistrictValidator;
use Repository\CityRepository;
use Repository\DistrictRepository;
use Service\Importer;
use Service\CityIterator;

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
));
$application->run();
