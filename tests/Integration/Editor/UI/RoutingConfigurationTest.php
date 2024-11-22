<?php

declare(strict_types=1);

namespace Districts\Test\Integration\Editor\UI;

use DI\Container;
use Districts\Editor\UI\RoutingConfiguration;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Slim\App;

/**
 * @covers \Districts\Editor\UI\RoutingConfiguration
 */
class RoutingConfigurationTest extends TestCase
{
    /**
     * @var App<ContainerInterface>
     */
    private App $app;

    protected function setUp(): void
    {
        /** @var ContainerInterface */
        $container = new Container();
        $this->app = new App(new Psr17Factory(), $container);
    }

    public function testApply(): void
    {
        RoutingConfiguration::apply($this->app);
        $routeCollector = $this->app->getRouteCollector();
        $this->assertNotEmpty($routeCollector->getRoutes());
    }

    /**
     * @dataProvider namedDataProvider
     */
    public function testNamed(string $name): void
    {
        RoutingConfiguration::apply($this->app);
        $routeCollector = $this->app->getRouteCollector();
        try {
            $routeCollector->getNamedRoute($name);
            $exceptionThrown = false;
        } catch (RuntimeException) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function namedDataProvider(): array
    {
        return [
            ["list"],
            ["add"],
            ["edit"],
            ["remove"],
        ];
    }
}
