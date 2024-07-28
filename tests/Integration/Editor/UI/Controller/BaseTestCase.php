<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use DI\Container;
use Districts\Editor\UI\RoutingConfiguration;
use Districts\Test\Integration\FixtureTool;
use Doctrine\ORM\EntityManager;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;

abstract class BaseTestCase extends TestCase
{
    protected function runApp(string $requestMethod, string $requestUri, array $requestData = []): ResponseInterface
    {
        $container = new Container();
        $app = $this->createApp($container);

        $entityManager = (require __DIR__ . "/../../../../../doctrine-bootstrap.php")();
        $container->set(EntityManager::class, $entityManager);

        FixtureTool::reset($entityManager);
        FixtureTool::loadFiles($entityManager, [
            "tests/Integration/Editor/Infrastructure/data/cities.sql",
            "tests/Integration/Editor/Infrastructure/data/districts.sql",
        ]);

        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }

    protected function createApp(Container $container): App
    {
        foreach (["common", "web"] as $dependencyPart) {
            $dependencies = require __DIR__ . "/../../../../../dependencies/{$dependencyPart}.php";
            foreach ($dependencies as $dependency => $factory) {
                $container->set($dependency, $factory);
            }
        }

        /** @var ResponseFactoryInterface */
        $responseFactory = $container->get(ResponseFactoryInterface::class);
        /** @var CallableResolverInterface */
        $callableResolver = $container->get(CallableResolverInterface::class);
        /** @var RouteCollectorInterface */
        $routeCollector = $container->get(RouteCollectorInterface::class);
        $app = new App($responseFactory, $container, $callableResolver, $routeCollector);

        $middleware = require __DIR__ . "/../../../../../src/middleware.php";
        $middleware($app);

        $app = RoutingConfiguration::apply($app);

        return $app;
    }

    protected function createRequest(string $method, string $uri, array $data): ServerRequestInterface
    {
        $inputUri = new Uri($uri);
        $queryParams = [];
        parse_str($inputUri->getQuery(), $queryParams);
        $requestUri = (new Uri())->withScheme('http')->withHost('localhost')->withPath($inputUri->getPath());
        $request = (new ServerRequest($method, $requestUri))->withQueryParams($queryParams);
        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }
        return $request;
    }
}
