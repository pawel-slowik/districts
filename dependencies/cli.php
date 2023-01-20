<?php

declare(strict_types=1);

use Districts\Scraper\Domain\HtmlFetcher;
use Districts\Scraper\Infrastructure\GuzzleHtmlFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return [
    HtmlFetcher::class => function ($container) {
        return $container->get(GuzzleHtmlFetcher::class);
    },
    ClientInterface::class => function ($container) {
        return $container->get(Client::class);
    },
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Client::class => function ($container) {
        return new Client(["verify" => false]);
    },
];
