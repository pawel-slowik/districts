<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Command;

readonly class UpdateDistrictCommand
{
    public function __construct(
        public int $id,
        public string $name,
        public float $area,
        public int $population,
    ) {
    }
}
