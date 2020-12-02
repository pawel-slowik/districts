<?php

declare(strict_types=1);

namespace Application\Command;

final class RemoveDistrictCommand
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
