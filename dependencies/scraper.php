<?php

declare(strict_types=1);

use Districts\Scraper\Application\ScraperCollection;
use Districts\Scraper\Domain\Gdansk\CityScraper as GdanskScraper;
use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Domain\Krakow\CityScraper as KrakowScraper;
use Districts\Scraper\Infrastructure\PsrHtmlFetcher;
use GuzzleHttp\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

use function DI\create;
use function DI\get;

return [
    ScraperCollection::class => create()->constructor(
        get(GdanskScraper::class),
        get(KrakowScraper::class),
    ),
    HtmlFetcher::class => get(PsrHtmlFetcher::class),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    ClientInterface::class => static fn ($container) => new Client(["verify" => false]),
    RequestFactoryInterface::class => get(Psr17Factory::class),
];
