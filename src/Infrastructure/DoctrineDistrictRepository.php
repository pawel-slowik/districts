<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\DomainModel\District;
use Districts\DomainModel\DistrictFilter\AreaFilter;
use Districts\DomainModel\DistrictFilter\CityNameFilter;
use Districts\DomainModel\DistrictFilter\Filter;
use Districts\DomainModel\DistrictFilter\NameFilter;
use Districts\DomainModel\DistrictFilter\PopulationFilter;
use Districts\DomainModel\DistrictOrdering;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\PaginatedResult;
use Districts\DomainModel\Pagination;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;

final class DoctrineDistrictRepository implements DistrictRepository
{
    private EntityManager $entityManager;

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
        ?Filter $filter = null,
        ?Pagination $pagination = null
    ): PaginatedResult {
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
            /** @var District[] $districts */
            $districts = iterator_to_array($paginator);
            $recordsTotal = count($paginator);
            $pageSize = $pagination->getPageSize();
        } else {
            /** @var District[] $districts */
            $districts = $query->getResult();
            $recordsTotal = count($districts);
            $pageSize = ($recordsTotal === 0) ? 1 : $recordsTotal;
        }
        return new PaginatedResult($pageSize, $recordsTotal, $districts);
    }

    private function dqlOrderBy(DistrictOrdering $order): string
    {
        $orderDqlMap = [
            DistrictOrdering::FULL_NAME => [
                DistrictOrdering::ASC => "c.name ASC, d.name.name ASC",
                DistrictOrdering::DESC => "c.name DESC, d.name.name DESC",
            ],
            DistrictOrdering::CITY_NAME => [
                DistrictOrdering::ASC => "c.name ASC",
                DistrictOrdering::DESC => "c.name DESC",
            ],
            DistrictOrdering::DISTRICT_NAME => [
                DistrictOrdering::ASC => "d.name.name ASC",
                DistrictOrdering::DESC => "d.name.name DESC",
            ],
            DistrictOrdering::AREA => [
                DistrictOrdering::ASC => "d.area.area ASC",
                DistrictOrdering::DESC => "d.area.area DESC",
            ],
            DistrictOrdering::POPULATION => [
                DistrictOrdering::ASC => "d.population.population ASC",
                DistrictOrdering::DESC => "d.population.population DESC",
            ],
        ];
        return $orderDqlMap[$order->getField()][$order->getDirection()];
    }

    private function dqlFilter(?Filter $filter): array
    {
        if (!$filter) {
            return ["", []];
        }

        if ($filter instanceof CityNameFilter) {
            return [
                " c.name LIKE :search",
                [
                    "search" => $this->dqlLike($filter->getCityName()),
                ],
            ];
        }

        if ($filter instanceof NameFilter) {
            return [
                " d.name.name LIKE :search",
                [
                    "search" => $this->dqlLike($filter->getName()),
                ],
            ];
        }

        if ($filter instanceof AreaFilter) {
            return [
                " d.area.area >= :low AND d.area.area <= :high",
                [
                    "low" => $filter->getBegin(),
                    "high" => $filter->getEnd(),
                ],
            ];
        }

        if ($filter instanceof PopulationFilter) {
            return [
                " d.population.population >= :low AND d.population.population <= :high",
                [
                    "low" => $filter->getBegin(),
                    "high" => $filter->getEnd(),
                ],
            ];
        }

        throw new InvalidArgumentException();
    }

    private function dqlLike(string $string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
