<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use DI\Container;
use Districts\UI\Web\RoutingConfiguration;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slim\App;

/**
 * @covers \Districts\UI\Web\RoutingConfiguration
 */
class RoutingConfigurationTest extends TestCase
{
    private App $app;

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
        } catch (RuntimeException $ex) {
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
