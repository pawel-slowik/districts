<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use DomainModel\Entity\City;

final class CityRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(int $id): ?City
    {
        return $this->entityManager->getRepository(City::class)->find($id);
    }

    public function findByName(string $name): ?City
    {
        return $this->entityManager->getRepository(City::class)->findOneBy(["name" => $name]);
    }

    public function list(): array
    {
        return $this->entityManager->getRepository(City::class)->findAll();
    }

    public function add(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }
}
