<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\DomainModel\DistrictFilter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\Entity\District;
use Districts\DomainModel\PagedResult;
use Districts\DomainModel\Pagination;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class DoctrineDistrictRepository implements DistrictRepository
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(int $id): District
    {
        $ormRepository = $this->entityManager->getRepository(District::class);
        $district = $ormRepository->find($id);
        if (!$district) {
            throw new NotFoundInRepositoryException();
        }
        return $district;
    }

    public function list(
        DistrictOrdering $order,
        ?DistrictFilter $filter = null,
        ?Pagination $pagination = null
    ): PagedResult {
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
        if ($pagination) {
            $query->setFirstResult(($pagination->getPageNumber() - 1) * $pagination->getPageSize());
            $query->setMaxResults($pagination->getPageSize());
            $paginator = new Paginator($query);
            $districts = iterator_to_array($paginator);
            $recordsTotal = count($paginator);
            $pageSize = $pagination->getPageSize();
        } else {
            $districts = $query->getResult();
            $recordsTotal = count($districts);
            $pageSize = ($recordsTotal === 0) ? 1 : $recordsTotal;
        }
        return new PagedResult($pageSize, $recordsTotal, $districts);
    }

    private function dqlOrderBy(DistrictOrdering $order): string
    {
        $orderDqlMap = [
            DistrictOrdering::FULL_NAME => [
                DistrictOrdering::ASC => "c.name ASC, d.name ASC",
                DistrictOrdering::DESC => "c.name DESC, d.name DESC",
            ],
            DistrictOrdering::CITY_NAME => [
                DistrictOrdering::ASC => "c.name ASC",
                DistrictOrdering::DESC => "c.name DESC",
            ],
            DistrictOrdering::DISTRICT_NAME => [
                DistrictOrdering::ASC => "d.name ASC",
                DistrictOrdering::DESC => "d.name DESC",
            ],
            DistrictOrdering::AREA => [
                DistrictOrdering::ASC => "d.area ASC",
                DistrictOrdering::DESC => "d.area DESC",
            ],
            DistrictOrdering::POPULATION => [
                DistrictOrdering::ASC => "d.population ASC",
                DistrictOrdering::DESC => "d.population DESC",
            ],
        ];
        return $orderDqlMap[$order->getField()][$order->getDirection()];
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
