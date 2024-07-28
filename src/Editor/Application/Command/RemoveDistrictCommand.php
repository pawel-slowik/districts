<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Command;

readonly class RemoveDistrictCommand
{
    public function __construct(
        public int $id,
    ) {
    }
}
