<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use Districts\Editor\Domain\Exception\InvalidAreaException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Area
{
    #[ORM\Column(type: "float")]
    private float $area;

    public function __construct(float $area)
    {
        if (!self::validate($area)) {
            throw new InvalidAreaException();
        }
        $this->area = $area;
    }

    public function __toString(): string
    {
        return strval($this->area);
    }

    public function equals(self $other): bool
    {
        return $this->area === $other->area;
    }

    public function asFloat(): float
    {
        return $this->area;
    }

    private static function validate(float $area): bool
    {
        if ($area <= 0) {
            return false;
        }
        return true;
    }
}
