<?php

declare(strict_types=1);

namespace Districts\Editor\UI\Factory;

use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class RemoveDistrictCommandFactory
{
    /**
     * @param array<string, string> $routeArgs
     */
    public function fromRoute(array $routeArgs, Request $request): RemoveDistrictCommand
    {
        if (!array_key_exists("id", $routeArgs)) {
            throw new HttpBadRequestException($request);
        }

        return new RemoveDistrictCommand(intval($routeArgs["id"]));
    }
}
