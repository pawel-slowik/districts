<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\ValidationException;

class GetDistrictQueryFactory
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function fromRequest(Request $request, array $routeArgs): GetDistrictQuery
    {
        $errors = [];
        if (array_key_exists("id", $routeArgs)) {
            $id = intval($routeArgs["id"]);
        } else {
            $errors[] = "id";
        }
        if (isset($id)) {
            return new GetDistrictQuery($id);
        }

        throw (new ValidationException())->withErrors($errors);
    }
}
