<?php

declare(strict_types=1);

use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Infrastructure\GuzzleHtmlFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return [
    HtmlFetcher::class => fn ($container) => $container->get(GuzzleHtmlFetcher::class),
    ClientInterface::class => fn ($container) => $container->get(Client::class),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Client::class => fn ($container) => new Client(["verify" => false]),
];
