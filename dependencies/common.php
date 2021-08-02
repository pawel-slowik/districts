<?php

declare(strict_types=1);

use DI\Container;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\DistrictRepository;
use Districts\Infrastructure\DoctrineCityRepository;
use Districts\Infrastructure\DoctrineDistrictRepository;
use Doctrine\ORM\EntityManager;

return function (Container $container): void {
    $dependencies = [

        // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        EntityManager::class => function ($container) {
            return (require __DIR__ . "/../doctrine-bootstrap.php")();
        },

        CityRepository::class => function ($container) {
            return $container->get(DoctrineCityRepository::class);
        },

        DistrictRepository::class => function ($container) {
            return $container->get(DoctrineDistrictRepository::class);
        },

    ];

    foreach ($dependencies as $dependency => $factory) {
        $container->set($dependency, $factory);
    }
};
