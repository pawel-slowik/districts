<?php

declare(strict_types=1);

namespace Districts\DomainModel\VO;

use Districts\DomainModel\Exception\InvalidNameException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Name
{
    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    public function __construct(string $name)
    {
        if (!self::validate($name)) {
            throw new InvalidNameException();
        }
        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function equals(self $other): bool
    {
        return $this->name === $other->name;
    }

    private static function validate(string $name): bool
    {
        if ($name === "") {
            return false;
        }
        return true;
    }
}
