<?php

declare(strict_types=1);

namespace DomainModel;

class DistrictOrdering
{
    public const DEFAULT = 0;
    public const CITY_ASC = 1;
    public const CITY_DESC = 2;
    public const NAME_ASC = 3;
    public const NAME_DESC = 4;
    public const AREA_ASC = 5;
    public const AREA_DESC = 6;
    public const POPULATION_ASC = 7;
    public const POPULATION_DESC = 8;

    private const VALID_ORDER_CHOICES = [
        self::DEFAULT,
        self::CITY_ASC,
        self::CITY_DESC,
        self::NAME_ASC,
        self::NAME_DESC,
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
