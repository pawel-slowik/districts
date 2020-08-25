<?php

declare(strict_types=1);

namespace DomainModel;

class DistrictFilter
{
    public const TYPE_CITY = 1;
    public const TYPE_NAME = 2;
    public const TYPE_AREA = 3;
    public const TYPE_POPULATION = 4;

    private const VALIDATORS = [
        self::TYPE_CITY => [self::class, "validateString"],
        self::TYPE_NAME => [self::class, "validateString"],
        self::TYPE_AREA => [self::class, "validateRange"],
        self::TYPE_POPULATION => [self::class, "validateRange"],
    ];

    private $type;

    private $value;

    public function __construct(int $type, $value)
    {
        if (!self::validate($type, $value)) {
            throw new \InvalidArgumentException();
        }
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

    private static function validate(int $type, $value): bool
    {
        if (!array_key_exists($type, self::VALIDATORS)) {
            return false;
        }
        return self::VALIDATORS[$type]($value);
    }

    private static function validateString($value): bool
    {
        return is_string($value) && ($value !== "");
    }

    private static function validateRange($value): bool
    {
        return
            is_array($value)
            && (count($value) === 2)
            && array_key_exists(0, $value)
            && array_key_exists(1, $value)
            && (is_float($value[0]) || is_int($value[0]))
            && (is_float($value[1]) || is_int($value[1]))
            && ($value[0] >= 0)
            && ($value[1] >= 0)
            && ($value[1] >= $value[0]);
    }
}
