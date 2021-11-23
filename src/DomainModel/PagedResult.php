<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use InvalidArgumentException;

class PagedResult
{
    private int $pageSize;

    private int $totalEntryCount;

    private array $currentPageEntries;

    public function __construct(int $pageSize, int $totalEntryCount, array $currentPageEntries)
    {
        if (!self::validate($pageSize, $totalEntryCount)) {
            throw new InvalidArgumentException();
        }
        $this->pageSize = $pageSize;
        $this->totalEntryCount = $totalEntryCount;
        $this->currentPageEntries = $currentPageEntries;
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
