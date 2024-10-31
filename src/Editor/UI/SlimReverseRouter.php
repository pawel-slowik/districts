<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Slim\Interfaces\RouteParserInterface;

class SlimReverseRouter implements ReverseRouter
{
    public function __construct(
        private RouteParserInterface $routeParser,
    ) {
    }

    public function urlFromRoute(string $routeName, array $routeData = []): string
    {
        return $this->routeParser->relativeUrlFor($routeName, $routeData);
    }
}
