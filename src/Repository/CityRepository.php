<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use Entity\City;

class CityRepository
{
    protected $ormRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->ormRepository = $entityManager->getRepository(City::class);
    }

    public function get(int $id): ?City
    {
        return $this->ormRepository->find($id);
    }

    public function list(): array
    {
        return $this->ormRepository->findAll();
    }
}
