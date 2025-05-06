<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\Doctrine;

use Districts\Core\Domain\District;
use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\DistrictRepository as DistrictRepositoryInterface;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Traversable;

final readonly class DistrictRepository implements DistrictRepositoryInterface
{
    public function __construct(
        private EntityManager $entityManager,
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
    public function listWithPagination(
        DistrictOrdering $order,
        Pagination $pagination,
        ?Filter $filter = null,
    ): PaginatedResult {
        $query = $this->createDqlQuery($order, $filter);
        $query->setFirstResult(($pagination->pageNumber - 1) * $pagination->pageSize);
        $query->setMaxResults($pagination->pageSize);
        $paginator = new Paginator($query);
        /** @var District[] $districts */
        $districts = iterator_to_array($paginator);
        $recordsTotal = count($paginator);
        $pageCount = intval(ceil($recordsTotal / $pagination->pageSize));
        return new PaginatedResult($pagination, $pageCount, $recordsTotal, $districts);
    }

    /**
     * @phpstan-ignore missingType.generics
     */
    private function createDqlQuery(
        DistrictOrdering $order,
        ?Filter $filter = null,
    ): Query {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select("d, c")->from(District::class, "d")->join("d.city", "c");
        if ($filter) {
            $dqlFilter = DistrictFilter::fromDomainFilter($filter);
            $queryBuilder->where($dqlFilter->where);
            foreach ($dqlFilter->parameters as $name => $value) {
                $queryBuilder->setParameter($name, $value);
            }
        }
        foreach ($this->dqlOrderBy($order) as [$field, $direction]) {
            $queryBuilder->addOrderBy($field, $direction);
        }
        return $queryBuilder->getQuery();
    }

    /**
     * @return Traversable<array{0: string, 1: string}>
     */
    private function dqlOrderBy(DistrictOrdering $order): Traversable
    {
        $fields = match ($order->field) {
            DistrictOrderingField::FullName => ["c.name", "d.name.name"],
            DistrictOrderingField::CityName => ["c.name"],
            DistrictOrderingField::DistrictName => ["d.name.name"],
            DistrictOrderingField::Area => ["d.area.area"],
            DistrictOrderingField::Population => ["d.population.population"],
        };
        $direction = match ($order->direction) {
            OrderingDirection::Asc => "ASC",
            OrderingDirection::Desc => "DESC",
        };
        foreach ($fields as $field) {
            yield [$field, $direction];
        }
    }
}
