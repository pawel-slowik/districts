<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use InvalidArgumentException;

class Pagination
{
    private int $pageNumber;

    private int $pageSize;

    public function __construct(int $pageNumber, int $pageSize)
    {
        if (!self::validate($pageNumber, $pageSize)) {
            throw new InvalidArgumentException();
        }
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    private static function validate(int $pageNumber, int $pageSize): bool
    {
        return ($pageNumber > 0) && ($pageSize > 0);
    }
}
