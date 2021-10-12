<?php

declare(strict_types=1);

namespace Districts\UI\Web\Factory;

use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;

class RemoveDistrictCommandFactory
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function fromRequest(Request $request, array $routeArgs): RemoveDistrictCommand
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
