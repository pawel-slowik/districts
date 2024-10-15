<?php

declare(strict_types=1);

use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Infrastructure\DoctrineCityRepository;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Doctrine\ORM\EntityManager;

return [
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    EntityManager::class => static fn ($container) => (require __DIR__ . "/../doctrine-bootstrap.php")(),
    CityRepository::class => static fn ($container) => $container->get(DoctrineCityRepository::class),
    DistrictRepository::class => static fn ($container) => $container->get(DoctrineDistrictRepository::class),
];
