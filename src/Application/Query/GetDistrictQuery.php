<?php

declare(strict_types=1);

namespace Districts\Application\Query;

class GetDistrictQuery
{
    public function __construct(
        private int $id,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
