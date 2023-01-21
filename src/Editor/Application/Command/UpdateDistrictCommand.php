<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Command;

class UpdateDistrictCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $area,
        public readonly int $population,
    ) {
    }
}
