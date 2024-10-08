<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateDistrictCommandFactory
{
    /**
     * @param array<string, string> $routeArgs
     */
    public function fromRequest(Request $request, array $routeArgs): UpdateDistrictCommand
    {
        $errors = [];
        if (array_key_exists("id", $routeArgs)) {
            $id = intval($routeArgs["id"]);
        } else {
            $errors[] = "id";
        }
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            if (array_key_exists("name", $parsedBody)) {
                $name = trim($parsedBody["name"]);
            } else {
                $errors[] = "name";
            }
            if (array_key_exists("area", $parsedBody)) {
                $area = floatval($parsedBody["area"]);
            } else {
                $errors[] = "area";
            }
            if (array_key_exists("population", $parsedBody)) {
                $population = intval($parsedBody["population"]);
            } else {
                $errors[] = "population";
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

        throw (new ValidationException())->withErrors($errors);
    }
}
