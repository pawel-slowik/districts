<?php

declare(strict_types=1);

namespace Service;

class DistrictFilter
{
    public const TYPE_CITY = 1;
    public const TYPE_NAME = 2;
    public const TYPE_AREA = 3;
    public const TYPE_POPULATION = 4;

    private $type;

    private $value;

    public function __construct(int $type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }
}
