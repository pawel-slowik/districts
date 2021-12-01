<?php

declare(strict_types=1);

namespace Districts\DomainModel\DistrictFilter;

use InvalidArgumentException;

class NameFilter extends Filter
{
    private string $name;

    public function __construct(string $name)
    {
        if (!self::validate($name)) {
            throw new InvalidArgumentException();
        }
        $this->name = $name;
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
