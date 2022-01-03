<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use InvalidArgumentException;

class Pagination
{
    public function __construct(
        private int $pageNumber,
        private int $pageSize,
    ) {
        if (!self::validate($pageNumber, $pageSize)) {
            throw new InvalidArgumentException();
        }
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
