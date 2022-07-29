<?php

declare(strict_types=1);

namespace Districts\Application\Query;

class GetDistrictQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}
