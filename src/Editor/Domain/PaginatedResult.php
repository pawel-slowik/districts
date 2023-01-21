<?php

declare(strict_types=1);

namespace Districts\Editor\Domain;

use InvalidArgumentException;

class PaginatedResult
{
    public function __construct(
        private int $pageSize,
        private int $totalEntryCount,
        private int $currentPageNumber,
        private array $currentPageEntries,
    ) {
        if (!self::validate($pageSize, $totalEntryCount, $currentPageNumber)) {
            throw new InvalidArgumentException();
        }
    }

    public function getCurrentPageEntries(): array
    {
        return $this->currentPageEntries;
    }

    public function getCurrentPageNumber(): int
    {
        return $this->currentPageNumber;
    }

    public function getPageCount(): int
    {
        return intval(ceil($this->totalEntryCount / $this->pageSize));
    }

    private static function validate(int $pageSize, int $totalEntryCount, int $currentPageNumber): bool
    {
        return ($pageSize > 0) && ($totalEntryCount >= 0) && ($currentPageNumber > 0);
    }
}
