<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class PagedResult implements ArrayAccess, Countable, IteratorAggregate
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

    public function count(): int
    {
        return count($this->currentPageEntries);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->currentPageEntries);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->currentPageEntries);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->currentPageEntries[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->currentPageEntries[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->currentPageEntries[$offset]);
    }

    private static function validate(int $pageSize, int $totalEntryCount): bool
    {
        return ($pageSize > 0) && ($totalEntryCount >= 0);
    }
}
