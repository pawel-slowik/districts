<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure;

abstract readonly class Filter
{
    /**
     * @param array<string, scalar> $parameters
     */
    protected function __construct(
        public string $where,
        public array $parameters,
    ) {
    }

    protected static function dqlLike(string $string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
