<?php

declare(strict_types=1);

namespace Districts;

use DI\Container;
use Psr\Container\ContainerInterface;

class DependencyContainerFactory
{
    /**
     * @param string[] $modules
     */
    public static function create(array $modules): ContainerInterface
    {
        $container = new Container();

        foreach ($modules as $dependencyPart) {
            $dependencies = require __DIR__ . "/../dependencies/{$dependencyPart}.php";
            foreach ($dependencies as $dependency => $factory) {
                $container->set($dependency, $factory);
            }
        }

        return $container;
    }
}
