<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\DomainModel\CityRepository;
use Districts\DomainModel\Entity\City;
use Doctrine\ORM\EntityManager;

final class DoctrineCityRepository implements CityRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(int $id): City
    {
        $city = $this->entityManager->getRepository(City::class)->find($id);
        if (!$city) {
            throw new NotFoundInRepositoryException();
        }
        return $city;
    }

    public function getByDistrictId(int $districtId): City
    {
        $dql = "SELECT c FROM " . City::class . " c JOIN c.districts d WHERE d.id = :id";
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter("id", $districtId);
        /** @var City[] $cities */
        $cities = $query->getResult();
        if (!$cities) {
            throw new NotFoundInRepositoryException();
        }
        return $cities[0];
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

    public function update(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
    }
}
