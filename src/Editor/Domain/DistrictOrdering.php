<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

class DistrictOrdering
{
    public function __construct(
        private DistrictOrderingField $field,
        private OrderingDirection $direction,
    ) {
    }

    public function getField(): DistrictOrderingField
    {
        return $this->field;
    }

    public function getDirection(): OrderingDirection
    {
        return $this->direction;
    }
}
