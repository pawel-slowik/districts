<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

class NameFilter extends Filter
{
    public function __construct(
        public readonly string $name,
    ) {
        if (!self::validate($name)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(string $name): bool
    {
        return $name !== "";
    }
}
