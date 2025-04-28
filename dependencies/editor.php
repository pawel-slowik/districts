<?php

declare(strict_types=1);

use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\App;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteCollector;
use Slim\Views\Twig;

use function DI\get;

return [
    DistrictRepository::class => get(DoctrineDistrictRepository::class),
    ResponseFactoryInterface::class => get(Psr17Factory::class),
    UriFactoryInterface::class => get(Psr17Factory::class),
    CallableResolverInterface::class => static fn ($container) => new CallableResolver($container),
    RouteCollectorInterface::class => static fn ($container) => new RouteCollector(
        $container->get(ResponseFactoryInterface::class),
        $container->get(CallableResolverInterface::class),
        $container,
    ),
    RouteParserInterface::class => static fn ($cont) => $cont->get(RouteCollectorInterface::class)->getRouteParser(),
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    Twig::class => static function ($container) {
        $twig = Twig::create(
            __DIR__ . "/../templates",
            [
                "cache" => "/tmp/twig_cache",
                "auto_reload" => true,
            ]
        );
        $twig["ASSETS_URL"] = (string) getenv("ASSETS_URL");
        return $twig;
    },
    App::class => static fn ($container) => new App(
        $container->get(ResponseFactoryInterface::class),
        null,
        $container->get(CallableResolverInterface::class),
        $container->get(RouteCollectorInterface::class),
    ),
];
