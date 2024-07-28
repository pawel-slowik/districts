<?php

declare(strict_types=1);

namespace Districts\Editor\Application\Query;

readonly class GetDistrictQuery
{
    public function __construct(
        public int $id,
    ) {
    }
}
