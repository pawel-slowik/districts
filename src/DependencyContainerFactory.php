<?php

declare(strict_types=1);

namespace Districts;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class DependencyContainerFactory
{
    /**
     * @param string[] $modules
     */
    public static function create(array $modules): ContainerInterface
    {
        $builder = new ContainerBuilder();
        foreach ($modules as $dependencyPart) {
            $builder->addDefinitions(__DIR__ . "/../dependencies/{$dependencyPart}.php");
        }
        return $builder->build();
    }
}
