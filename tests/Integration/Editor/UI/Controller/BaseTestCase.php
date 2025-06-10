<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI\Controller;

use Districts\DependencyContainerFactory;
use Districts\Editor\UI\Middleware;
use Districts\Editor\UI\RoutingConfiguration;
use Districts\Test\Integration\FixtureTool;
use Doctrine\ORM\EntityManager;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Views\Twig;

abstract class BaseTestCase extends TestCase
{
    /**
     * @param array<string, string> $requestData
     */
    protected function runApp(string $requestMethod, string $requestUri, array $requestData = []): ResponseInterface
    {
        $container = $this->createContainer();
        $app = $this->createApp($container);

        /** @var EntityManager */
        $entityManager = $container->get(EntityManager::class);
        FixtureTool::reset($entityManager);
        FixtureTool::loadFiles($entityManager, [
            "tests/Integration/Editor/data/cities.sql",
            "tests/Integration/Editor/data/districts.sql",
        ]);

        $request = $this->createRequest($requestMethod, $requestUri, $requestData);
        return $app->handle($request);
    }

    /**
     * @return App<null>
     */
    protected function createApp(ContainerInterface $container): App
    {
        /** @var Twig */
        $twig = $container->get(Twig::class);
        /** @var App<null> */
        $app = $container->get(App::class);

        Middleware::setUp($app, $twig);
        RoutingConfiguration::apply($app);

        return $app;
    }

    protected function createContainer(): ContainerInterface
    {
        return DependencyContainerFactory::create(["common", "editor"]);
    }

    /**
     * @param array<string, string> $data
     */
    protected function createRequest(string $method, string $uri, array $data): ServerRequestInterface
    {
        $inputUri = new Uri($uri);
        $queryParams = [];
        parse_str($inputUri->getQuery(), $queryParams);
        $requestUri = (new Uri())->withScheme('http')->withHost('localhost')->withPath($inputUri->getPath());
        $request = (new ServerRequest($method, $requestUri))->withQueryParams($queryParams);
        if ($data !== []) {
            $request = $request->withParsedBody($data);
        }
        return $request;
    }
}
