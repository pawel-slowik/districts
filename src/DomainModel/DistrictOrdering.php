<?php

declare(strict_types=1);

namespace DomainModel;

class DistrictOrdering
{
    public const FULL_NAME_ASC = 0;
    public const CITY_NAME_ASC = 1;
    public const CITY_NAME_DESC = 2;
    public const DISTRICT_NAME_ASC = 3;
    public const DISTRICT_NAME_DESC = 4;
    public const AREA_ASC = 5;
    public const AREA_DESC = 6;
    public const POPULATION_ASC = 7;
    public const POPULATION_DESC = 8;

    private const VALID_ORDER_CHOICES = [
        self::FULL_NAME_ASC,
        self::CITY_NAME_ASC,
        self::CITY_NAME_DESC,
        self::DISTRICT_NAME_ASC,
        self::DISTRICT_NAME_DESC,
        self::AREA_ASC,
        self::AREA_DESC,
        self::POPULATION_ASC,
        self::POPULATION_DESC,
    ];

    private $order;

    public function __construct(int $order)
    {
        if (!self::validate($order)) {
            throw new \InvalidArgumentException();
        }
        $this->order = $order;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    private static function validate(int $order): bool
    {
        return array_key_exists($order, self::VALID_ORDER_CHOICES);
    }
}
