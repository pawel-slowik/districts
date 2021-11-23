<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Districts\Application\Query\ListDistrictsQuery;
use Psr\Http\Message\ServerRequestInterface as Request;

class ListDistrictsQueryFactory
{
    private DistrictOrderingFactory $orderingFactory;

    private DistrictFilterFactory $filterFactory;

    private PaginationFactory $paginationFactory;

    public function __construct(
        DistrictOrderingFactory $orderingFactory,
        DistrictFilterFactory $filterFactory,
        PaginationFactory $paginationFactory
    ) {
        $this->orderingFactory = $orderingFactory;
        $this->filterFactory = $filterFactory;
        $this->paginationFactory = $paginationFactory;
    }

    public function fromRequest(Request $request, array $routeArgs): ListDistrictsQuery
    {
        $orderColumn = $routeArgs["column"] ?? null;
        $orderDirection = $routeArgs["direction"] ?? null;
        $queryParams = $request->getQueryParams();
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
