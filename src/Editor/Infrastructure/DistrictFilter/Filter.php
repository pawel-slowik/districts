<?php

declare(strict_types=1);

namespace Districts\Editor\Infrastructure\DistrictFilter;

abstract class Filter
{
    abstract public function where(): string;

    abstract public function parameters(): array;

    protected function dqlLike(string $string): string
    {
        // Doctrine will handle SQL injections, we just need to escape the LIKE syntax
        return "%" . addcslashes($string, "%_") . "%";
    }
}
