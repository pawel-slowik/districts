<?php

declare(strict_types=1);

use Districts\UI\Web\ReverseRouter;
use Districts\UI\Web\SlimReverseRouter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteCollector;
use Slim\Views\Twig;

return [
    ResponseFactoryInterface::class => function ($container) {
        return $container->get(Psr17Factory::class);
    },
    CallableResolverInterface::class => function ($container) {
        return new CallableResolver($container);
    },
    RouteCollectorInterface::class => function ($container) {
        return new RouteCollector(
            $container->get(ResponseFactoryInterface::class),
            $container->get(CallableResolverInterface::class),
            $container
        );
    },
    RouteParserInterface::class => function ($container) {
        return $container->get(RouteCollectorInterface::class)->getRouteParser();
    },
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Twig::class => function ($container) {
        return Twig::create(
            __DIR__ . "/../templates",
            [
                "cache" => "/tmp/twig_cache",
                "auto_reload" => true,
            ]
        );
    },
    ReverseRouter::class => function ($container) {
        return $container->get(SlimReverseRouter::class);
    },
];
