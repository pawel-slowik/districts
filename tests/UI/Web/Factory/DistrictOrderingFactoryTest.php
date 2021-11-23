<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\Factory;

use Districts\DomainModel\DistrictOrdering;
use Districts\UI\Web\Factory\DistrictOrderingFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\UI\Web\Factory\DistrictOrderingFactory
 */
class DistrictOrderingFactoryTest extends TestCase
{
    private DistrictOrderingFactory $districtOrderingFactory;

    protected function setUp(): void
    {
        $this->districtOrderingFactory = new DistrictOrderingFactory();
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ?string $inputColumn,
        ?string $inputDirection,
        int $expectedField,
        int $expectedDirection
    ): void {
        $order = $this->districtOrderingFactory->createFromRequestInput($inputColumn, $inputDirection);
        $this->assertInstanceOf(DistrictOrdering::class, $order);
        $this->assertSame($expectedField, $order->getField());
        $this->assertSame($expectedDirection, $order->getDirection());
    }

    public function createDataProvider(): array
    {
        return [
            [
                "city", null, DistrictOrdering::FULL_NAME, DistrictOrdering::ASC,
            ],
            [
                null, "asc", DistrictOrdering::FULL_NAME, DistrictOrdering::ASC,
            ],
            [
                "foo", "bar", DistrictOrdering::FULL_NAME, DistrictOrdering::ASC,
            ],
            [
                "city", "foo", DistrictOrdering::FULL_NAME, DistrictOrdering::ASC,
            ],
            [
                "bar", "asc", DistrictOrdering::FULL_NAME, DistrictOrdering::ASC,
            ],
            [
                "city", "asc", DistrictOrdering::CITY_NAME, DistrictOrdering::ASC,
            ],
            [
                "city", "desc", DistrictOrdering::CITY_NAME, DistrictOrdering::DESC,
            ],
            [
                "name", "asc", DistrictOrdering::DISTRICT_NAME, DistrictOrdering::ASC,
            ],
            [
                "name", "desc", DistrictOrdering::DISTRICT_NAME, DistrictOrdering::DESC,
            ],
            [
                "area", "asc", DistrictOrdering::AREA, DistrictOrdering::ASC,
            ],
            [
                "area", "desc", DistrictOrdering::AREA, DistrictOrdering::DESC,
            ],
            [
                "population", "asc", DistrictOrdering::POPULATION, DistrictOrdering::ASC,
            ],
            [
                "population", "desc", DistrictOrdering::POPULATION, DistrictOrdering::DESC,
            ],
        ];
    }
}
