<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Psr\Http\Message\ServerRequestInterface as Request;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\ValidationException;

class UpdateDistrictCommandFactory
{
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
        if (isset(
            $id,
            $name,
            $area,
            $population,
        )) {
            return new UpdateDistrictCommand($id, $name, $area, $population);
        }

        throw (new ValidationException())->withErrors($errors);
    }
}
