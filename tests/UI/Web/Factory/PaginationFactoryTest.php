<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\DomainModel\Pagination;
use Districts\UI\Web\Factory\PaginationFactory;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Factory\PaginationFactory
 */
class PaginationFactoryTest extends TestCase
{
    /**
     * @var PaginationFactory
     */
    private $paginationFactory;

    protected function setUp(): void
    {
        $this->paginationFactory = new PaginationFactory();
    }

    public function testCreateFromValid(): void
    {
        $pagination = $this->paginationFactory->createFromRequestInput("5");
        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertSame(5, $pagination->getPageNumber());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testCreateFromInvalidReturnsFirstPage(?string $invalidPage): void
    {
        $pagination = $this->paginationFactory->createFromRequestInput($invalidPage);
        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertSame(1, $pagination->getPageNumber());
    }

    public function invalidDataProvider(): array
    {
        return [
            "null" => [null],
            "not_parseable_as_integer" => ["foo"],
            "zero" => ["0"],
            "lower_than_zero" => ["-1"],
        ];
    }
}
