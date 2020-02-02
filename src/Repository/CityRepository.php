<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use Entity\City;

class CityRepository
{
    protected $entityManager;

    protected $ormRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->ormRepository = $entityManager->getRepository(City::class);
    }

    public function get(int $id): ?City
    {
        return $this->ormRepository->find($id);
    }

    public function findByName(string $name): ?City
    {
        return $this->ormRepository->findOneBy(["name" => $name]);
    }

    public function list(): array
    {
        return $this->ormRepository->findAll();
    }

    public function add(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }
}
