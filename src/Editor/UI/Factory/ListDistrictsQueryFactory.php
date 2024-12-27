<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Query\ListDistrictsQuery;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListDistrictsQueryFactory
{
    public function __construct(
        private DistrictOrderingFactory $orderingFactory,
        private DistrictFilterFactory $filterFactory,
        private PaginationFactory $paginationFactory,
    ) {
    }

    public function fromRequest(Request $request): ListDistrictsQuery
    {
        $queryParams = $request->getQueryParams();
        $orderColumn = $queryParams["orderColumn"] ?? null;
        $orderDirection = $queryParams["orderDirection"] ?? null;
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        $pageNumber = $queryParams["page"] ?? null;
        return new ListDistrictsQuery(
            $this->orderingFactory->createFromRequestInput($orderColumn, $orderDirection),
            $this->filterFactory->createFromRequestInput($filterColumn, $filterValue),
            $this->paginationFactory->createFromRequestInput($pageNumber),
        );
    }

    public function fromDefaults(): ListDistrictsQuery
    {
        return new ListDistrictsQuery(
            $this->orderingFactory->createFromRequestInput(null, null),
            $this->filterFactory->createFromRequestInput(null, null),
            $this->paginationFactory->createFromRequestInput(null),
        );
    }
}
