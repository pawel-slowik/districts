<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure;

use Districts\Core\Domain\District;
use Districts\Core\Infrastructure\NotFoundInRepositoryException;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\Infrastructure\DistrictFilter\FilterFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Traversable;

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
     * @return District[]
     */
    public function list(
        DistrictOrdering $order,
        ?Filter $filter = null,
    ): array {
        $query = $this->createDqlQuery($order, $filter);
        /** @var District[] $districts */
        $districts = $query->getResult();
        return $districts;
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
        $dqlFilter = $this->filterFactory->fromDomainFilter($filter);
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select("d, c")->from(District::class, "d")->join("d.city", "c");
        if ($dqlFilter) {
            $queryBuilder->where($dqlFilter->where());
            foreach ($dqlFilter->parameters() as $name => $value) {
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
