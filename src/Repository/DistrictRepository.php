<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use Entity\City;
use Entity\District;

class DistrictRepository
{
    public const ORDER_DEFAULT = 0;
    public const ORDER_CITY_ASC = 1;
    public const ORDER_CITY_DESC = 2;
    public const ORDER_NAME_ASC = 3;
    public const ORDER_NAME_DESC = 4;
    public const ORDER_AREA_ASC = 5;
    public const ORDER_AREA_DESC = 6;
    public const ORDER_POPULATION_ASC = 7;
    public const ORDER_POPULATION_DESC = 8;

    protected $orderDqlMap = [
        self::ORDER_DEFAULT => "c.name ASC, d.name ASC",
        self::ORDER_CITY_ASC => "c.name ASC",
        self::ORDER_CITY_DESC => "c.name DESC",
        self::ORDER_NAME_ASC => "d.name ASC",
        self::ORDER_NAME_DESC => "d.name DESC",
        self::ORDER_AREA_ASC => "d.area ASC",
        self::ORDER_AREA_DESC => "d.area DESC",
        self::ORDER_POPULATION_ASC => "d.population ASC",
        self::ORDER_POPULATION_DESC => "d.population DESC",
    ];

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(int $id): ?District
    {
        $ormRepository = $this->entityManager->getRepository(District::class);
        return $ormRepository->find($id);
    }

    public function remove(District $district): void
    {
        $this->entityManager->remove($district);
        $this->entityManager->flush();
    }

    public function add(City $city, District $district): void
    {
        $district->setCity($city);
        $this->entityManager->persist($district);
        $this->entityManager->flush();
    }

    public function list($orderBy): array
    {
        $dqlOrderBy = $this->dqlOrderBy($orderBy);
        $dql = "SELECT d, c FROM " . District::class . " d JOIN d.city c ORDER BY " . $dqlOrderBy;
        $query = $this->entityManager->createQuery($dql);
        $districts = $query->getResult();
        return $districts;
    }

    public function getCity(int $id): ?City
    {
        $ormRepository = $this->entityManager->getRepository(City::class);
        return $ormRepository->find($id);
    }

    public function listCities(): array
    {
        $ormRepository = $this->entityManager->getRepository(City::class);
        return $ormRepository->findAll();
    }

    protected function dqlOrderBy($orderBy): string
    {
        if (!is_scalar($orderBy) || !array_key_exists($orderBy, $this->orderDqlMap)) {
            $orderBy = self::ORDER_DEFAULT;
        }
        return $this->orderDqlMap[$orderBy];
    }
}
