<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Controller;

use DI\Container;
use Districts\Test\Infrastructure\FixtureTool;
use Districts\UI\Web\RoutingConfiguration;
use Doctrine\ORM\EntityManager;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

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
            "tests/Infrastructure/data/cities.sql",
            "tests/Infrastructure/data/districts.sql",
        ]);

        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }

    protected function createApp(ContainerInterface $container): App
    {
        $app = new App(new Psr17Factory(), $container);

        $dependencies = require __DIR__ . "/../../../../src/dependencies.php";
        $dependencies($container, $app);

        $middleware = require __DIR__ . "/../../../../src/middleware.php";
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
