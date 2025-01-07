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
        public int $pageSize,
        public int $totalEntryCount,
        public int $currentPageNumber,
        public array $currentPageEntries,
    ) {
        if (!self::validate($pageSize, $totalEntryCount, $currentPageNumber)) {
            throw new InvalidArgumentException();
        }
        $this->pageCount = intval(ceil($this->totalEntryCount / $this->pageSize));
    }

    private static function validate(int $pageSize, int $totalEntryCount, int $currentPageNumber): bool
    {
        return ($pageSize > 0) && ($totalEntryCount >= 0) && ($currentPageNumber > 0);
    }
}
