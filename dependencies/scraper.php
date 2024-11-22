<?php

declare(strict_types=1);

use Districts\Scraper\Domain\Gdansk\CityScraper as GdanskScraper;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\Krakow\CityScraper as KrakowScraper;
use Districts\Scraper\Infrastructure\GuzzleHtmlFetcher;
use Districts\Scraper\UI\ImportCommand;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

use function DI\autowire;
use function DI\get;

return [
    ImportCommand::class => autowire()->constructor(
        scrapers: [
            get(GdanskScraper::class),
            get(KrakowScraper::class),
        ],
    ),
    HtmlFetcher::class => get(GuzzleHtmlFetcher::class),
    ClientInterface::class => get(Client::class),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Client::class => static fn ($container) => new Client(["verify" => false]),
];
