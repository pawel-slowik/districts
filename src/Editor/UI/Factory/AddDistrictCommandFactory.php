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
        if (!is_array($parsedBody)) {
            throw new HttpBadRequestException($request);
        }

        if (
            array_key_exists("city", $parsedBody)
            && array_key_exists("name", $parsedBody)
            && array_key_exists("area", $parsedBody)
            && array_key_exists("population", $parsedBody)
        ) {
            return new AddDistrictCommand(
                intval($parsedBody["city"]),
                trim($parsedBody["name"]),
                floatval($parsedBody["area"]),
                intval($parsedBody["population"]),
            );
        }

        throw new HttpBadRequestException($request);
    }
}
