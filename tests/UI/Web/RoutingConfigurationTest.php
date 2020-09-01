<?php

declare(strict_types=1);

namespace Test\UI\Web;

use DI\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App;
use UI\Web\RoutingConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @covers \UI\Web\RoutingConfiguration
 */
class RoutingConfigurationTest extends TestCase
{
    /**
     * @var App
     */
    private $app;

    protected function setUp(): void
    {
        $this->app = new App(new Psr17Factory(), new Container());
    }

    public function testApply(): void
    {
        $app = RoutingConfiguration::apply($this->app);
        $routeCollector = $app->getRouteCollector();
        $this->assertNotEmpty($routeCollector->getRoutes());
    }

    /**
     * @dataProvider namedDataProvider
     */
    public function testNamed(string $name): void
    {
        $app = RoutingConfiguration::apply($this->app);
        $routeCollector = $app->getRouteCollector();
        try {
            $routeCollector->getNamedRoute($name);
            $exceptionThrown = false;
        } catch (\RuntimeException $ex) {
            $exceptionThrown = true;
        }
        $this->assertFalse($exceptionThrown);
    }

    public function namedDataProvider(): array
    {
        return [
            ["list"],
            ["add"],
            ["edit"],
            ["remove"],
        ];
    }
}
