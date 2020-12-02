<?php

declare(strict_types=1);

namespace UI\Web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Application\Command\RemoveDistrictCommand;
use Service\ValidationException;

class RemoveDistrictCommandFactory
{
    public function fromRequest(Request $request, array $routeArgs): RemoveDistrictCommand
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
