#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Command\UpdateCommand;
use Repository\CityRepository;
use Repository\DistrictRepository;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$application = new Application();
$application->add(new UpdateCommand(
    new CityRepository($entityManager),
    new DistrictRepository($entityManager)
));
$application->run();
