<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Query\GetDistrictQuery;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

final class GetDistrictQueryFactory
{
    /**
     * @param array<string, string> $routeArgs
     */
    public function fromRoute(array $routeArgs, Request $request): GetDistrictQuery
    {
        if (!array_key_exists("id", $routeArgs)) {
            throw new HttpBadRequestException($request);
        }

        return new GetDistrictQuery(intval($routeArgs["id"]));
    }
}
