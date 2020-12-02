<?php

declare(strict_types=1);

namespace UI\Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Application\Command\AddDistrictCommand;
use Application\Command\RemoveDistrictCommand;
use Application\Command\UpdateDistrictCommand;
use Service\ValidationException;

class RequestParser
{
    public function parseAdd(Request $request): AddDistrictCommand
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

    public function parseUpdate(Request $request, array $routeArgs): UpdateDistrictCommand
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

    public function parseRemove(Request $request, array $routeArgs): RemoveDistrictCommand
    {
        $errors = [];
        if (array_key_exists("id", $routeArgs)) {
            $id = intval($routeArgs["id"]);
        } else {
            $errors[] = "id";
        }
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            $isConfirmed = array_key_exists("confirm", $parsedBody);
        }
        if (isset(
            $id,
            $isConfirmed,
        )) {
            return new RemoveDistrictCommand($id, $isConfirmed);
        }

        throw (new ValidationException())->withErrors($errors);
    }
}
