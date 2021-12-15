<?php

declare(strict_types=1);

namespace Districts\UI\Web;

use Psr\Http\Message\UriInterface;

interface ReverseRouter
{
    public function urlFromRoute(UriInterface $baseUri, string $routeName, array $routeData = []): string;
}
