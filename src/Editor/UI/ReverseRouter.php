<?php

declare(strict_types=1);

namespace Districts\Editor\UI;

interface ReverseRouter
{
    /**
     * @param array<string, string> $routeData
     */
    public function urlFromRoute(string $routeName, array $routeData = []): string;
}
