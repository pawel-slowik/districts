<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class AddDistrictCommand
{
    public function __construct(
        public readonly int $cityId,
        public readonly string $name,
        public readonly float $area,
        public readonly int $population,
    ) {
    }
}
