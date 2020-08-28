<?php

declare(strict_types=1);

namespace Test\Service;

use DomainModel\DistrictOrdering;
use Service\DistrictOrderingFactory;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Service\DistrictOrderingFactory
 */
class DistrictOrderingFactoryTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ?string $inputColumn,
        ?string $inputDirection,
        int $expectedOrder
    ): void {
        $order = DistrictOrderingFactory::createFromRequestInput($inputColumn, $inputDirection);
        $this->assertInstanceOf(DistrictOrdering::class, $order);
        $this->assertSame($expectedOrder, $order->getOrder());
    }

    public function createDataProvider(): array
    {
        return [
            [
                "city", null, DistrictOrdering::FULL_NAME_ASC,
            ],
            [
                null, "asc", DistrictOrdering::FULL_NAME_ASC,
            ],
            [
                "foo", "bar", DistrictOrdering::FULL_NAME_ASC,
            ],
            [
                "city", "foo", DistrictOrdering::FULL_NAME_ASC,
            ],
            [
                "bar", "asc", DistrictOrdering::FULL_NAME_ASC,
            ],
            [
                "city", "asc", DistrictOrdering::CITY_NAME_ASC,
            ],
            [
                "city", "desc", DistrictOrdering::CITY_NAME_DESC,
            ],
            [
                "name", "asc", DistrictOrdering::DISTRICT_NAME_ASC,
            ],
            [
                "name", "desc", DistrictOrdering::DISTRICT_NAME_DESC,
            ],
            [
                "area", "asc", DistrictOrdering::AREA_ASC,
            ],
            [
                "area", "desc", DistrictOrdering::AREA_DESC,
            ],
            [
                "population", "asc", DistrictOrdering::POPULATION_ASC,
            ],
            [
                "population", "desc", DistrictOrdering::POPULATION_DESC,
            ],
        ];
    }
}
