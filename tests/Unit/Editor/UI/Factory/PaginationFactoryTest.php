<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\PaginationFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaginationFactory::class)]
class PaginationFactoryTest extends TestCase
{
    private PaginationFactory $paginationFactory;

    protected function setUp(): void
    {
        $this->paginationFactory = new PaginationFactory();
    }

    public function testCreateFromValid(): void
    {
        $pagination = $this->paginationFactory->createFromRequestInput("5");
        $this->assertSame(5, $pagination->pageNumber);
    }

    #[DataProvider('invalidDataProvider')]
    public function testCreateFromInvalidReturnsFirstPage(?string $invalidPage): void
    {
        $pagination = $this->paginationFactory->createFromRequestInput($invalidPage);
        $this->assertSame(1, $pagination->pageNumber);
    }

    /**
     * @return array<string, array{0: null|string}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            "null" => [null],
            "not_parseable_as_integer" => ["foo"],
            "zero" => ["0"],
            "lower_than_zero" => ["-1"],
        ];
    }
}
