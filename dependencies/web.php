<?php

declare(strict_types=1);

use Districts\Editor\UI\ReverseRouter;
use Districts\Editor\UI\SlimReverseRouter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteCollector;
use Slim\Views\Twig;

return [
    ResponseFactoryInterface::class => static fn ($container) => $container->get(Psr17Factory::class),
    CallableResolverInterface::class => static fn ($container) => new CallableResolver($container),
    RouteCollectorInterface::class => static function ($container) {
        return new RouteCollector(
            $container->get(ResponseFactoryInterface::class),
            $container->get(CallableResolverInterface::class),
            $container
        );
    },
    RouteParserInterface::class => static fn ($cont) => $cont->get(RouteCollectorInterface::class)->getRouteParser(),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Twig::class => static function ($container) {
        return Twig::create(
            __DIR__ . "/../templates",
            [
                "cache" => "/tmp/twig_cache",
                "auto_reload" => true,
            ]
        );
    },
    ReverseRouter::class => static fn ($container) => $container->get(SlimReverseRouter::class),
];
