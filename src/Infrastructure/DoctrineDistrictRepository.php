<?php

declare(strict_types=1);

namespace Districts\Infrastructure;

use Districts\Domain\District;
use Districts\Domain\DistrictFilter\Filter;
use Districts\Domain\DistrictOrdering;
use Districts\Domain\DistrictRepository;
use Districts\Domain\PaginatedResult;
use Districts\Domain\Pagination;
use Districts\Infrastructure\DistrictFilter\FilterFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class DoctrineDistrictRepository implements DistrictRepository
{
    public function __construct(
        private EntityManager $entityManager,
        private FilterFactory $filterFactory,
    ) {
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
        $dqlFilter = $this->filterFactory->fromDomainFilter($filter);
        $dql = "SELECT d, c FROM " . District::class . " d JOIN d.city c";
        if ($dqlFilter->where() !== "") {
            $dql .= " WHERE " . $dqlFilter->where();
        }
        $dql .= " ORDER BY " . $dqlOrderBy;
        $query = $this->entityManager->createQuery($dql);
        if ($dqlFilter->parameters()) {
            foreach ($dqlFilter->parameters() as $name => $value) {
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
            $pageNumber = $pagination->getPageNumber();
        } else {
            /** @var District[] $districts */
            $districts = $query->getResult();
            $recordsTotal = count($districts);
            $pageSize = ($recordsTotal === 0) ? 1 : $recordsTotal;
            $pageNumber = 1;
        }
        return new PaginatedResult($pageSize, $recordsTotal, $pageNumber, $districts);
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
}
