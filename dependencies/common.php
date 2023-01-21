<?php

declare(strict_types=1);

use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Infrastructure\DoctrineCityRepository;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Doctrine\ORM\EntityManager;

return [
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
