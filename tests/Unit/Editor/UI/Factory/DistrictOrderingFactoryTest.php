<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\UI\Factory\DistrictOrderingFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictOrderingFactory::class)]
final class DistrictOrderingFactoryTest extends TestCase
{
    private DistrictOrderingFactory $districtOrderingFactory;

    protected function setUp(): void
    {
        $this->districtOrderingFactory = new DistrictOrderingFactory();
    }

    #[DataProvider('createDataProvider')]
    public function testCreate(
        ?string $inputColumn,
        ?string $inputDirection,
        DistrictOrderingField $expectedField,
        OrderingDirection $expectedDirection,
    ): void {
        $order = $this->districtOrderingFactory->createFromRequestInput($inputColumn, $inputDirection);
        $this->assertSame($expectedField, $order->field);
        $this->assertSame($expectedDirection, $order->direction);
    }

    /**
     * @return array<array{0: ?string, 1: ?string, 2: DistrictOrderingField, 3: OrderingDirection}>
     */
    public static function createDataProvider(): array
    {
        return [
            [
                "city", null, DistrictOrderingField::FullName, OrderingDirection::Asc,
            ],
            [
                null, "asc", DistrictOrderingField::FullName, OrderingDirection::Asc,
            ],
            [
                "foo", "bar", DistrictOrderingField::FullName, OrderingDirection::Asc,
            ],
            [
                "city", "foo", DistrictOrderingField::FullName, OrderingDirection::Asc,
            ],
            [
                "bar", "asc", DistrictOrderingField::FullName, OrderingDirection::Asc,
            ],
            [
                "city", "asc", DistrictOrderingField::CityName, OrderingDirection::Asc,
            ],
            [
                "city", "desc", DistrictOrderingField::CityName, OrderingDirection::Desc,
            ],
            [
                "name", "asc", DistrictOrderingField::DistrictName, OrderingDirection::Asc,
            ],
            [
                "name", "desc", DistrictOrderingField::DistrictName, OrderingDirection::Desc,
            ],
            [
                "area", "asc", DistrictOrderingField::Area, OrderingDirection::Asc,
            ],
            [
                "area", "desc", DistrictOrderingField::Area, OrderingDirection::Desc,
            ],
            [
                "population", "asc", DistrictOrderingField::Population, OrderingDirection::Asc,
            ],
            [
                "population", "desc", DistrictOrderingField::Population, OrderingDirection::Desc,
            ],
        ];
    }
}
