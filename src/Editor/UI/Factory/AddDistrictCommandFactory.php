<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class AddDistrictCommandFactory
{
    public function fromRequest(Request $request): AddDistrictCommand
    {
        $parsedBody = $request->getParsedBody();
        if (is_array($parsedBody)) {
            if (array_key_exists("city", $parsedBody)) {
                $cityId = intval($parsedBody["city"]);
            }
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
                $cityId,
                $name,
                $area,
                $population,
            )
        ) {
            return new AddDistrictCommand($cityId, $name, $area, $population);
        }

        throw new HttpBadRequestException($request);
    }
}
