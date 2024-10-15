<?php

declare(strict_types=1);

use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Infrastructure\GuzzleHtmlFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return [
    HtmlFetcher::class => static fn ($container) => $container->get(GuzzleHtmlFetcher::class),
    ClientInterface::class => static fn ($container) => $container->get(Client::class),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Client::class => static fn ($container) => new Client(["verify" => false]),
];
