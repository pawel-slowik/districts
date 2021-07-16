#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Console\Application;
use Districts\UI\CLI\UpdateCommand;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Application\Importer;
use Districts\Infrastructure\GuzzleHtmlFetcher;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();

$application = new Application();
$application->add(new UpdateCommand(
    new Importer(new DoctrineCityRepository($entityManager)),
    new GuzzleHtmlFetcher(),
));
$application->run();
