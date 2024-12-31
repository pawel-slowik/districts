<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Districts\Editor\Domain\DistrictOrdering
 */
class DistrictOrderingTest extends TestCase
{
    public function testGetters(): void
    {
        $order = new DistrictOrdering(DistrictOrderingField::CityName, OrderingDirection::Asc);
        $this->assertSame(DistrictOrderingField::CityName, $order->field);
        $this->assertSame(OrderingDirection::Asc, $order->direction);
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
     * @return Traversable<array{0: DistrictOrderingField, 1: OrderingDirection}>
     */
    public static function validDataProvider(): Traversable
    {
        foreach (DistrictOrderingField::cases() as $field) {
            foreach (OrderingDirection::cases() as $direction) {
                yield [$field, $direction];
            }
        }
    }
}
