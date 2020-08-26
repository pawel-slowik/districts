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
                "city", null, DistrictOrdering::DEFAULT,
            ],
            [
                null, "asc", DistrictOrdering::DEFAULT,
            ],
            [
                "foo", "bar", DistrictOrdering::DEFAULT,
            ],
            [
                "city", "foo", DistrictOrdering::DEFAULT,
            ],
            [
                "bar", "asc", DistrictOrdering::DEFAULT,
            ],
            [
                "city", "asc", DistrictOrdering::CITY_ASC,
            ],
            [
                "city", "desc", DistrictOrdering::CITY_DESC,
            ],
            [
                "name", "asc", DistrictOrdering::NAME_ASC,
            ],
            [
                "name", "desc", DistrictOrdering::NAME_DESC,
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
