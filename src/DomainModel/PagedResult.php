<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use InvalidArgumentException;

class PagedResult
{
    private $pageSize;

    private $totalEntryCount;

    private $currentPageEntries;

    public function __construct(int $pageSize, int $totalEntryCount, array $currentPageEntries)
    {
        if (!self::validate($pageSize, $totalEntryCount)) {
            throw new InvalidArgumentException();
        }
        $this->pageSize = $pageSize;
        $this->totalEntryCount = $totalEntryCount;
        $this->currentPageEntries = $currentPageEntries;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getTotalEntryCount(): int
    {
        return $this->totalEntryCount;
    }

    public function getCurrentPageEntries(): array
    {
        return $this->currentPageEntries;
    }

    public function getPageCount(): int
    {
        return intval(ceil($this->totalEntryCount / $this->pageSize));
    }

    private static function validate(int $pageSize, int $totalEntryCount): bool
    {
        return ($pageSize > 0) && ($totalEntryCount >= 0);
    }
}
