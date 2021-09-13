<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel;

use Districts\DomainModel\Pagination;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Pagination
 */
class PaginationTest extends TestCase
{
    public function testGetters(): void
    {
        $pagination = new Pagination(1, 2);
        $this->assertSame(1, $pagination->getPageNumber());
        $this->assertSame(2, $pagination->getPageSize());
    }

    public function testExceptionOnInvalidNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $result = new Pagination(0, 1);
    }

    public function testExceptionOnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $result = new Pagination(1, 0);
    }
}
