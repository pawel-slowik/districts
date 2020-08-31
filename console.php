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

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$application = new Application();
$application->add(new UpdateCommand(
    new Importer(
        new DistrictValidator(),
        new DistrictRepository($entityManager),
        new CityRepository($entityManager),
    ),
));
$application->run();
