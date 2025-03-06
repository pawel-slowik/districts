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
        if (array_key_exists("id", $routeArgs)) {
            $id = intval($routeArgs["id"]);
        }
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            if (array_key_exists("name", $parsedBody)) {
                $name = trim($parsedBody["name"]);
            }
            if (array_key_exists("area", $parsedBody)) {
                $area = floatval($parsedBody["area"]);
            }
            if (array_key_exists("population", $parsedBody)) {
                $population = intval($parsedBody["population"]);
            }
        }
        if (
            isset(
                $id,
                $name,
                $area,
                $population,
            )
        ) {
            return new UpdateDistrictCommand($id, $name, $area, $population);
        }

        throw new HttpBadRequestException($request);
    }
}
