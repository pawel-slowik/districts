<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\Application\Query\ListDistrictsQuery;

class ListDistrictsQueryFactory
{
    private $orderingFactory;

    private $filterFactory;

    public function __construct(
        DistrictOrderingFactory $orderingFactory,
        DistrictFilterFactory $filterFactory
    ) {
        $this->orderingFactory = $orderingFactory;
        $this->filterFactory = $filterFactory;
    }

    public function fromRequest(Request $request, array $routeArgs): ListDistrictsQuery
    {
        $orderColumn = $routeArgs["column"] ?? null;
        $orderDirection = $routeArgs["direction"] ?? null;
        $queryParams = $request->getQueryParams();
        $filterColumn = $queryParams["filterColumn"] ?? null;
        $filterValue = $queryParams["filterValue"] ?? null;
        return new ListDistrictsQuery(
            $this->orderingFactory->createFromRequestInput($orderColumn, $orderDirection),
            $this->filterFactory->createFromRequestInput($filterColumn, $filterValue),
        );
    }
}
