<?php

declare(strict_types=1);

namespace Districts\Core\Domain;

use Districts\Core\Domain\Exception\InvalidPopulationException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
readonly class Population
{
    #[ORM\Column(type: "integer")]
    private int $population;

    public function __construct(int $population)
    {
        if (!self::validate($population)) {
            throw new InvalidPopulationException();
        }
        $this->population = $population;
    }

    public function __toString(): string
    {
        return strval($this->population);
    }

    public function equals(self $other): bool
    {
        return $this->population === $other->population;
    }

    private static function validate(int $population): bool
    {
        if ($population <= 0) {
            return false;
        }
        return true;
    }
}
