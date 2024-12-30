<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Domain\DistrictOrdering
 */
class DistrictOrderingTest extends TestCase
{
    public function testGetters(): void
    {
        $order = new DistrictOrdering(DistrictOrderingField::CityName, OrderingDirection::Asc);
        $this->assertSame(DistrictOrderingField::CityName, $order->getField());
        $this->assertSame(OrderingDirection::Asc, $order->getDirection());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(DistrictOrderingField $field, OrderingDirection $direction): void
    {
        $order = new DistrictOrdering($field, $direction);
        $this->assertInstanceOf(DistrictOrdering::class, $order);
    }

    /**
     * @return array<array{0: DistrictOrderingField, 1: OrderingDirection}>
     */
    public static function validDataProvider(): array
    {
        return [
            [DistrictOrderingField::FullName, OrderingDirection::Asc],
            [DistrictOrderingField::FullName, OrderingDirection::Desc],
            [DistrictOrderingField::CityName, OrderingDirection::Asc],
            [DistrictOrderingField::CityName, OrderingDirection::Desc],
            [DistrictOrderingField::DistrictName, OrderingDirection::Asc],
            [DistrictOrderingField::DistrictName, OrderingDirection::Desc],
            [DistrictOrderingField::Area, OrderingDirection::Asc],
            [DistrictOrderingField::Area, OrderingDirection::Desc],
            [DistrictOrderingField::Population, OrderingDirection::Asc],
            [DistrictOrderingField::Population, OrderingDirection::Desc],
        ];
    }
}
