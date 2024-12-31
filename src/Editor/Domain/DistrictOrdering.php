<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

readonly class DistrictOrdering
{
    public function __construct(
        public DistrictOrderingField $field,
        public OrderingDirection $direction,
    ) {
    }
}
