<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaginatedResult::class)]
final class PaginatedResultTest extends TestCase
{
    public function testExceptionOnInvalidPageCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult($this->createStub(Pagination::class), -1, 1, []);
    }

    public function testExceptionOnInvalidTotalCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginatedResult($this->createStub(Pagination::class), 1, -1, []);
    }
}
