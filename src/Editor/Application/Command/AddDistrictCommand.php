<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Command;

readonly class AddDistrictCommand
{
    public function __construct(
        public int $cityId,
        public string $name,
        public float $area,
        public int $population,
    ) {
    }
}
