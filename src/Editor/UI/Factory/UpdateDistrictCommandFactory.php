<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class UpdateDistrictCommandFactory
{
    /**
     * @param array<string, string> $routeArgs
     */
    public function fromRequestAndRoute(Request $request, array $routeArgs): UpdateDistrictCommand
    {
        if (!array_key_exists("id", $routeArgs)) {
            throw new HttpBadRequestException($request);
        }

        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            throw new HttpBadRequestException($request);
        }

        if (
            array_key_exists("name", $parsedBody)
            && array_key_exists("area", $parsedBody)
            && array_key_exists("population", $parsedBody)
        ) {
            return new UpdateDistrictCommand(
                intval($routeArgs["id"]),
                trim($parsedBody["name"]),
                floatval($parsedBody["area"]),
                intval($parsedBody["population"]),
            );
        }

        throw new HttpBadRequestException($request);
    }
}
