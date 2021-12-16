<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use InvalidArgumentException;

class PaginatedResult
{
    private int $pageSize;

    private int $totalEntryCount;

    private int $currentPageNumber;

    private array $currentPageEntries;

    public function __construct(int $pageSize, int $totalEntryCount, int $currentPageNumber, array $currentPageEntries)
    {
        if (!self::validate($pageSize, $totalEntryCount, $currentPageNumber)) {
            throw new InvalidArgumentException();
        }
        $this->pageSize = $pageSize;
        $this->totalEntryCount = $totalEntryCount;
        $this->currentPageNumber = $currentPageNumber;
        $this->currentPageEntries = $currentPageEntries;
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
