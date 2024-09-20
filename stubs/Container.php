<?php

declare(strict_types=1);

namespace DI;

class Container
{
    /**
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return T
     */
    public function get(string $id): mixed
    {
    }
}
