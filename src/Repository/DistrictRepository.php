<?php

declare(strict_types=1);

namespace Repository;

use Doctrine\ORM\EntityManager;

use DomainModel\Entity\District;
use DomainModel\DistrictFilter;
use DomainModel\DistrictOrdering;

final class DistrictRepository
{
    private const ORDER_DQL_MAP = [
        DistrictOrdering::FULL_NAME_ASC => "c.name ASC, d.name ASC",
        DistrictOrdering::CITY_NAME_ASC => "c.name ASC",
        DistrictOrdering::CITY_NAME_DESC => "c.name DESC",
        DistrictOrdering::DISTRICT_NAME_ASC => "d.name ASC",
        DistrictOrdering::DISTRICT_NAME_DESC => "d.name DESC",
        DistrictOrdering::AREA_ASC => "d.area ASC",
        DistrictOrdering::AREA_DESC => "d.area DESC",
        DistrictOrdering::POPULATION_ASC => "d.population ASC",
        DistrictOrdering::POPULATION_DESC => "d.population DESC",
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

    public function list(DistrictOrdering $order, ?DistrictFilter $filter = null): array
    {
        $dqlOrderBy = $this->dqlOrderBy($order);
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

    private function dqlOrderBy(DistrictOrdering $order): string
    {
        return self::ORDER_DQL_MAP[$order->getOrder()];
    }

    private function dqlFilter(?DistrictFilter $filter): array
    {
        if (!$filter) {
            return ["", []];
        }
        $filterValue = $filter->getValue();
        switch ($filter->getType()) {
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
