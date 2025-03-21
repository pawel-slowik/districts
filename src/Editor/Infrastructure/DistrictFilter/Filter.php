<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

abstract readonly class Filter
{
    abstract public function where(): string;

    /**
     * @return array<string, scalar>
     */
    abstract public function parameters(): array;

    protected function dqlLike(string $string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
