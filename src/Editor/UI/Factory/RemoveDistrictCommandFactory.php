<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Districts\Editor\Application\Exception\ValidationException;

class RemoveDistrictCommandFactory
{
    public function fromRoute(array $routeArgs): RemoveDistrictCommand
    {
        $errors = [];
        if (array_key_exists("id", $routeArgs)) {
            $id = intval($routeArgs["id"]);
        } else {
            $errors[] = "id";
        }

        if (isset($id)) {
            return new RemoveDistrictCommand($id);
        }

        throw (new ValidationException())->withErrors($errors);
    }
}
