<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use InvalidArgumentException;

/**
 * @template T
 */
readonly class PaginatedResult
{
    /**
     * @param T[] $currentPageEntries
     */
    public function __construct(
        public Pagination $pagination,
        public int $pageCount,
        public int $totalEntryCount,
        public array $currentPageEntries,
    ) {
        if (!self::validate($pageCount, $totalEntryCount)) {
            throw new InvalidArgumentException();
        }
    }

    private static function validate(int $pageCount, int $totalEntryCount): bool
    {
        return ($pageCount >= 0) && ($totalEntryCount >= 0);
    }
}
