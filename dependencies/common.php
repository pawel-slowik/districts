<?php

declare(strict_types=1);

use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Infrastructure\DoctrineCityRepository;
use Districts\Editor\Infrastructure\DoctrineDistrictRepository;
use Districts\Infrastructure\Doctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManager;

use function DI\get;

return [
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    EntityManager::class => static fn ($container) => EntityManagerFactory::create(__DIR__ . '/../src'),
    CityRepository::class => get(DoctrineCityRepository::class),
    DistrictRepository::class => get(DoctrineDistrictRepository::class),
];
