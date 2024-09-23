<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

use Psr\Http\Message\UriInterface;

interface ReverseRouter
{
    /**
     * @param array<string, string> $routeData
     */
    public function urlFromRoute(UriInterface $baseUri, string $routeName, array $routeData = []): string;
}
