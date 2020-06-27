<?php

declare(strict_types=1);

namespace Test\UI\Web\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use Slim\App;
use UI\Web\RoutingConfiguration;
use PHPUnit\Framework\TestCase;
use Test\Repository\FixtureTool;
use Doctrine\ORM\EntityManager;

abstract class BaseTestCase extends TestCase
{
    protected function runApp(string $requestMethod, string $requestUri, ?array $requestData = []): ResponseInterface
    {
        $container = new Container();
        $app = $this->createApp($container);

        $entityManager = (require __DIR__ . "/../../../../doctrine-bootstrap.php")();
        $container->set(EntityManager::class, $entityManager);

        FixtureTool::reset($entityManager);
        FixtureTool::load($entityManager, [
            "tests/Repository/data/cities.sql",
            "tests/Repository/data/districts.sql",
        ]);

        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }

    private function createApp(ContainerInterface $container): App
    {
        $app = new App(new Psr17Factory(), $container);

        $dependencies = require __DIR__ . "/../../../../src/dependencies.php";
        $dependencies($container, $app);

        $middleware = require __DIR__ . "/../../../../src/middleware.php";
        $middleware($app);

        $app = RoutingConfiguration::apply($app);

        return $app;
    }

    private function createRequest(string $method, string $uri, array $data): ServerRequestInterface
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
