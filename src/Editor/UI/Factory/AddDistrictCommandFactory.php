<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddDistrictCommandFactory
{
    public function fromRequest(Request $request): AddDistrictCommand
    {
        $errors = [];
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            if (array_key_exists("city", $parsedBody)) {
                $cityId = intval($parsedBody["city"]);
            } else {
                $errors[] = "city";
            }
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
            $cityId,
            $name,
            $area,
            $population,
        )) {
            return new AddDistrictCommand($cityId, $name, $area, $population);
        }

        throw (new ValidationException())->withErrors($errors);
    }
}
