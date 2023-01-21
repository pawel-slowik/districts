<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

class SlimReverseRouter implements ReverseRouter
{
    public function __construct(
        private RouteParserInterface $routeParser,
    ) {
    }

    public function urlFromRoute(UriInterface $baseUri, string $routeName, array $routeData = []): string
    {
        return $this->routeParser->fullUrlFor($baseUri, $routeName, $routeData);
    }
}
