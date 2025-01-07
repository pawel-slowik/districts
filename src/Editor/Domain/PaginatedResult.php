<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use InvalidArgumentException;

/**
 * @template T
 */
readonly class PaginatedResult
{
    public int $pageCount;

    /**
     * @param T[] $currentPageEntries
     */
    public function __construct(
        public Pagination $pagination,
        public int $totalEntryCount,
        public array $currentPageEntries,
    ) {
        if (!self::validate($totalEntryCount)) {
            throw new InvalidArgumentException();
        }
        $this->pageCount = intval(ceil($this->totalEntryCount / $this->pagination->pageSize));
    }

    private static function validate(int $totalEntryCount): bool
    {
        return $totalEntryCount >= 0;
    }
}
