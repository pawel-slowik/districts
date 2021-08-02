#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Districts\Application\Importer;
use Districts\DomainModel\Scraper\Gdansk\CityParser as GdanskCityParser;
use Districts\DomainModel\Scraper\Gdansk\CityScraper as GdanskScraper;
use Districts\DomainModel\Scraper\Gdansk\DistrictParser as GdanskDistrictParser;
use Districts\DomainModel\Scraper\HtmlFinder;
use Districts\DomainModel\Scraper\Krakow\CityParser as KrakowCityParser;
use Districts\DomainModel\Scraper\Krakow\CityScraper as KrakowScraper;
use Districts\DomainModel\Scraper\Krakow\DistrictParser as KrakowDistrictParser;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\GuzzleHtmlFetcher;
use Districts\UI\CLI\ImportCommand;
use GuzzleHttp\Client;
use Symfony\Component\Console\Application;

$entityManagerFactory = require "doctrine-bootstrap.php";
$entityManager = $entityManagerFactory();
$fetcher = new GuzzleHtmlFetcher(new Client());
$finder = new HtmlFinder();

$application = new Application();
$application->add(
    new ImportCommand(
        new Importer(new DoctrineCityRepository($entityManager)),
        [
            new GdanskScraper($fetcher, new GdanskCityParser($finder), new GdanskDistrictParser($finder)),
            new KrakowScraper($fetcher, new KrakowCityParser($finder), new KrakowDistrictParser($finder)),
        ]
    )
);
$application->run();
