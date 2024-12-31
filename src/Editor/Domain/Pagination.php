<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use InvalidArgumentException;

readonly class Pagination
{
    public function __construct(
        public int $pageNumber,
        public int $pageSize,
    ) {
        if (!self::validate($pageNumber, $pageSize)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(int $pageNumber, int $pageSize): bool
    {
        return ($pageNumber > 0) && ($pageSize > 0);
    }
}
