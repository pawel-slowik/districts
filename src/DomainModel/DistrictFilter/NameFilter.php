<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class NameFilter extends Filter
{
    public function __construct(
        private string $name,
    ) {
        if (!self::validate($name)) {
            throw new InvalidArgumentException();
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    private static function validate(string $name): bool
    {
        return $name !== "";
    }
}
