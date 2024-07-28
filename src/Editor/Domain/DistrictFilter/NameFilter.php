<?php

declare(strict_types=1);

namespace Districts\Editor\Domain\DistrictFilter;

use InvalidArgumentException;

readonly class NameFilter extends Filter
{
    public function __construct(
        public string $name,
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
