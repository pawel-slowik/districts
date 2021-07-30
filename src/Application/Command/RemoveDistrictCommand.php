<?php

declare(strict_types=1);

namespace Districts\Application\Command;

class RemoveDistrictCommand
{
    private $id;

    private $isConfirmed;

    public function __construct(int $id, bool $isConfirmed)
    {
        $this->id = $id;
        $this->isConfirmed = $isConfirmed;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }
}
