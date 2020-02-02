<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use Entity\District;

class DistrictRepository
{
    public const FILTER_NONE = 0;
    public const FILTER_CITY = 1;
    public const FILTER_NAME = 2;
    public const FILTER_AREA = 3;
    public const FILTER_POPULATION = 4;

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

    public function add(District $district): void
    {
        $this->entityManager->persist($district);
        $this->entityManager->flush();
    }

    public function update(District $district): void
    {
        $this->entityManager->persist($district);
        $this->entityManager->flush();
    }

    public function list($orderBy, $filterType = self::FILTER_NONE, $filterValue = null): array
    {
        $dqlOrderBy = $this->dqlOrderBy($orderBy);
        list($dqlWhere, $dqlParameters) = $this->dqlFilter($filterType, $filterValue);
        $dql = "SELECT d, c FROM " . District::class . " d JOIN d.city c";
        if ($dqlWhere !== "") {
            $dql .= " WHERE " . $dqlWhere;
        }
        $dql .= " ORDER BY " . $dqlOrderBy;
        $query = $this->entityManager->createQuery($dql);
        if ($dqlParameters) {
            foreach ($dqlParameters as $name => $value) {
                $query->setParameter($name, $value);
            }
        }
        $districts = $query->getResult();
        return $districts;
    }

    protected function dqlOrderBy($orderBy): string
    {
        if (!is_scalar($orderBy) || !array_key_exists($orderBy, $this->orderDqlMap)) {
            $orderBy = self::ORDER_DEFAULT;
        }
        return $this->orderDqlMap[$orderBy];
    }

    protected function dqlFilter($filterType, $filterValue): array
    {
        switch ($filterType) {
            case self::FILTER_CITY:
                return [
                    " c.name LIKE :search",
                    [
                        "search" => $this->dqlLike($filterValue),
                    ],
                ];
            case self::FILTER_NAME:
                return [
                    " d.name LIKE :search",
                    [
                        "search" => $this->dqlLike($filterValue),
                    ],
                ];
            case self::FILTER_AREA:
                return [
                    " d.area >= :low AND d.area <= :high",
                    [
                        "low" => $filterValue[0],
                        "high" => $filterValue[1],
                    ],
                ];
            case self::FILTER_POPULATION:
                return [
                    " d.population >= :low AND d.population <= :high",
                    [
                        "low" => $filterValue[0],
                        "high" => $filterValue[1],
                    ],
                ];
        }
        return ["", []];
    }

    protected function dqlLike($string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
