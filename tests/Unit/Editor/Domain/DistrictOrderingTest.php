<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Domain;

use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Traversable;

#[CoversClass(DistrictOrdering::class)]
class DistrictOrderingTest extends TestCase
{
    public function testProperties(): void
    {
        $order = new DistrictOrdering(DistrictOrderingField::CityName, OrderingDirection::Asc);
        $this->assertSame(DistrictOrderingField::CityName, $order->field);
        $this->assertSame(OrderingDirection::Asc, $order->direction);
    }

    #[DataProvider('validDataProvider')]
    public function testValid(DistrictOrderingField $field, OrderingDirection $direction): void
    {
        try {
            new DistrictOrdering($field, $direction); // @phpstan-ignore new.resultUnused
            $exceptionThrown = false;
        } catch (InvalidArgumentException) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
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
