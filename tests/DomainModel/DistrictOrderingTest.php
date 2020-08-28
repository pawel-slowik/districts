<?php

declare(strict_types=1);

namespace Test\DomainModel;

use DomainModel\DistrictOrdering;

use PHPUnit\Framework\TestCase;

/**
 * @covers \DomainModel\DistrictOrdering
 */
class DistrictOrderingTest extends TestCase
{
    public function testGetters(): void
    {
        $order = new DistrictOrdering(DistrictOrdering::CITY_NAME_ASC);
        $this->assertSame(DistrictOrdering::CITY_NAME_ASC, $order->getOrder());
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testValid(int $testOrder): void
    {
        $order = new DistrictOrdering($testOrder);
        $this->assertInstanceOf(DistrictOrdering::class, $order);
    }

    public function validDataProvider(): array
    {
        return [
            [DistrictOrdering::FULL_NAME_ASC],
            [DistrictOrdering::FULL_NAME_DESC],
            [DistrictOrdering::CITY_NAME_ASC],
            [DistrictOrdering::CITY_NAME_DESC],
            [DistrictOrdering::DISTRICT_NAME_ASC],
            [DistrictOrdering::DISTRICT_NAME_DESC],
            [DistrictOrdering::AREA_ASC],
            [DistrictOrdering::AREA_DESC],
            [DistrictOrdering::POPULATION_ASC],
            [DistrictOrdering::POPULATION_DESC],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testInvalid(int $testOrder): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DistrictOrdering($testOrder);
    }

    public function invalidDataProvider(): array
    {
        return [
            [-1],
            [0],
            [11],
        ];
    }
}
