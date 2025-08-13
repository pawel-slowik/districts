<?php

declare(strict_types=1);

use Districts\Core\Domain\CityRepository as CityRepositoryInterface;
use Districts\Core\Infrastructure\Doctrine\CityRepository;
use Districts\Core\Infrastructure\Doctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManager;

use function DI\get;

return [
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    EntityManager::class => static fn ($container) => EntityManagerFactory::create(__DIR__ . '/../../src'),
    CityRepositoryInterface::class => get(CityRepository::class),
];
