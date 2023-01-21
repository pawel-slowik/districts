<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use InvalidArgumentException;

class DistrictOrdering
{
    public const FULL_NAME = 1;

    public const CITY_NAME = 2;

    public const DISTRICT_NAME = 3;

    public const AREA = 4;

    public const POPULATION = 5;

    public const ASC = 1;

    public const DESC = 2;

    private const VALID_FIELDS = [
        self::FULL_NAME,
        self::CITY_NAME,
        self::DISTRICT_NAME,
        self::AREA,
        self::POPULATION,
    ];

    private const VALID_DIRECTIONS = [
        self::ASC,
        self::DESC,
    ];

    public function __construct(
        private int $field,
        private int $direction,
    ) {
        if (!self::validate($field, $direction)) {
            throw new InvalidArgumentException();
        }
    }

    public function getField(): int
    {
        return $this->field;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    private static function validate(int $field, int $direction): bool
    {
        return in_array($field, self::VALID_FIELDS, true) && in_array($direction, self::VALID_DIRECTIONS, true);
    }
}
