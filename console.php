#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Districts\Application\Importer;
use Districts\DomainModel\Scraper\Gdansk\CityScraper as GdanskScraper;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\CityScraper as KrakowScraper;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\GuzzleHtmlFetcher;
use Districts\UI\CLI\ImportCommand;
use Symfony\Component\Console\Application;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();
$fetcher = new GuzzleHtmlFetcher();
$finder = new HtmlFinder();

$application = new Application();
$application->add(
    new ImportCommand(
        new Importer(new DoctrineCityRepository($entityManager)),
        [
            new GdanskScraper($fetcher, $finder),
            new KrakowScraper($fetcher, $finder),
        ]
    )
);
$application->run();
