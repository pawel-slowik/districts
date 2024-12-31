<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure;

use Districts\Editor\Domain\District;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
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

    /**
     * @return PaginatedResult<District>
     */
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
            $query->setFirstResult(($pagination->pageNumber - 1) * $pagination->pageSize);
            $query->setMaxResults($pagination->pageSize);
            $paginator = new Paginator($query);
            /** @var District[] $districts */
            $districts = iterator_to_array($paginator);
            $recordsTotal = count($paginator);
            $pageSize = $pagination->pageSize;
            $pageNumber = $pagination->pageNumber;
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
        return match ([$order->field, $order->direction]) {
            [DistrictOrderingField::FullName, OrderingDirection::Asc] => "c.name ASC, d.name.name ASC",
            [DistrictOrderingField::FullName, OrderingDirection::Desc] => "c.name DESC, d.name.name DESC",

            [DistrictOrderingField::CityName, OrderingDirection::Asc] => "c.name ASC",
            [DistrictOrderingField::CityName, OrderingDirection::Desc] => "c.name DESC",

            [DistrictOrderingField::DistrictName, OrderingDirection::Asc] => "d.name.name ASC",
            [DistrictOrderingField::DistrictName, OrderingDirection::Desc] => "d.name.name DESC",

            [DistrictOrderingField::Area, OrderingDirection::Asc] => "d.area.area ASC",
            [DistrictOrderingField::Area, OrderingDirection::Desc] => "d.area.area DESC",

            [DistrictOrderingField::Population, OrderingDirection::Asc] => "d.population.population ASC",
            [DistrictOrderingField::Population, OrderingDirection::Desc] => "d.population.population DESC",
        };
    }
}
