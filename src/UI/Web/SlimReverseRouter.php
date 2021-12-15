<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

class SlimReverseRouter implements ReverseRouter
{
    private RouteParserInterface $routeParser;

    public function __construct(RouteParserInterface $routeParser)
    {
        $this->routeParser = $routeParser;
    }

    public function urlFromRoute(UriInterface $baseUri, string $routeName, array $routeData = []): string
    {
        return $this->routeParser->fullUrlFor($baseUri, $routeName, $routeData);
    }
}
