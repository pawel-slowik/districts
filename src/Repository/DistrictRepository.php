<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;

final class DistrictRepository
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

    private const ORDER_DQL_MAP = [
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

    private $entityManager;

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

    public function removeMultiple(iterable $districts): void
    {
        foreach ($districts as $district) {
            $this->entityManager->remove($district);
        }
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

    public function list(int $orderBy, ?DistrictFilter $filter = null): array
    {
        $dqlOrderBy = $this->dqlOrderBy($orderBy);
        list($dqlWhere, $dqlParameters) = $this->dqlFilter($filter);
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

    private function dqlOrderBy(int $orderBy): string
    {
        if (!array_key_exists($orderBy, self::ORDER_DQL_MAP)) {
            $orderBy = self::ORDER_DEFAULT;
        }
        return self::ORDER_DQL_MAP[$orderBy];
    }

    private function dqlFilter(?DistrictFilter $filter): array
    {
        if (!$filter) {
            return ["", []];
        }
        $filterType = $filter->getType();
        $filterValue = $filter->getValue();
        switch ($filterType) {
            case DistrictFilter::TYPE_CITY:
                return [
                    " c.name LIKE :search",
                    [
                        "search" => $this->dqlLike($filterValue),
                    ],
                ];
            case DistrictFilter::TYPE_NAME:
                return [
                    " d.name LIKE :search",
                    [
                        "search" => $this->dqlLike($filterValue),
                    ],
                ];
            case DistrictFilter::TYPE_AREA:
                return [
                    " d.area >= :low AND d.area <= :high",
                    [
                        "low" => $filterValue[0],
                        "high" => $filterValue[1],
                    ],
                ];
            case DistrictFilter::TYPE_POPULATION:
                return [
                    " d.population >= :low AND d.population <= :high",
                    [
                        "low" => $filterValue[0],
                        "high" => $filterValue[1],
                    ],
                ];
        }

        throw new \InvalidArgumentException();
    }

    private function dqlLike(string $string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
