#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Districts\Application\Importer;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\GuzzleHtmlFetcher;
use Districts\UI\CLI\UpdateCommand;
use Symfony\Component\Console\Application;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$application = new Application();
$application->add(new UpdateCommand(
    new Importer(new DoctrineCityRepository($entityManager)),
    new GuzzleHtmlFetcher(),
));
$application->run();
