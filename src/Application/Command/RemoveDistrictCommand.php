<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class RemoveDistrictCommand
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
