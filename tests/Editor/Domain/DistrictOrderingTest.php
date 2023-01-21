<?php

declare(strict_types=1);

namespace Districts\Test\Editor\Domain;

use Districts\Editor\Domain\DistrictOrdering;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Domain\DistrictOrdering
 */
class DistrictOrderingTest extends TestCase
{
    public function testGetters(): void
    {
        $order = new DistrictOrdering(DistrictOrdering::CITY_NAME, DistrictOrdering::ASC);
        $this->assertSame(DistrictOrdering::CITY_NAME, $order->getField());
        $this->assertSame(DistrictOrdering::ASC, $order->getDirection());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(int $field, int $direction): void
    {
        $order = new DistrictOrdering($field, $direction);
        $this->assertInstanceOf(DistrictOrdering::class, $order);
    }

    public function validDataProvider(): array
    {
        return [
            [DistrictOrdering::FULL_NAME, DistrictOrdering::ASC],
            [DistrictOrdering::FULL_NAME, DistrictOrdering::DESC],
            [DistrictOrdering::CITY_NAME, DistrictOrdering::ASC],
            [DistrictOrdering::CITY_NAME, DistrictOrdering::DESC],
            [DistrictOrdering::DISTRICT_NAME, DistrictOrdering::ASC],
            [DistrictOrdering::DISTRICT_NAME, DistrictOrdering::DESC],
            [DistrictOrdering::AREA, DistrictOrdering::ASC],
            [DistrictOrdering::AREA, DistrictOrdering::DESC],
            [DistrictOrdering::POPULATION, DistrictOrdering::ASC],
            [DistrictOrdering::POPULATION, DistrictOrdering::DESC],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalid(int $field, int $direction): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DistrictOrdering($field, $direction);
    }

    public function invalidDataProvider(): array
    {
        return [
            "both invalid and negative" => [
                -1,
                -1,
            ],
            "field invalid and negative" => [
                -1,
                DistrictOrdering::ASC,
            ],
            "direction invalid and negative" => [
                DistrictOrdering::FULL_NAME,
                -1,
            ],
            "both invalid not negative" => [
                0,
                0,
            ],
            "field invalid not negative" => [
                0,
                DistrictOrdering::ASC,
            ],
            "direction invalid not negative" => [
                DistrictOrdering::FULL_NAME,
                0,
            ],
            "both too high" => [
                11,
                11,
            ],
            "field too high" => [
                11,
                DistrictOrdering::ASC,
            ],
            "direction too high" => [
                DistrictOrdering::FULL_NAME,
                11,
            ],
        ];
    }
}
