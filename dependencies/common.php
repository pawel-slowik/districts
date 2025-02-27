<?php

declare(strict_types=1);

use Districts\Core\Domain\CityRepository;
use Districts\Core\Infrastructure\DoctrineCityRepository;
use Districts\Core\Infrastructure\DoctrineEntityManagerFactory;
use Doctrine\ORM\EntityManager;

use function DI\get;

return [
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    EntityManager::class => static fn ($container) => DoctrineEntityManagerFactory::create(__DIR__ . '/../src'),
    CityRepository::class => get(DoctrineCityRepository::class),
];
