<?php

declare(strict_types=1);

use DI\Container;
use Districts\DomainModel\Scraper\HtmlFetcher;
use Districts\Infrastructure\GuzzleHtmlFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

return function (Container $container): void {
    $dependencies = [

        HtmlFetcher::class => function ($container) {
            return $container->get(GuzzleHtmlFetcher::class);
        },

        ClientInterface::class => function ($container) {
            return $container->get(Client::class);
        },

    ];

    foreach ($dependencies as $dependency => $factory) {
        $container->set($dependency, $factory);
    }
};
