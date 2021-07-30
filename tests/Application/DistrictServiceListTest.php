<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\DistrictService;
use Districts\Application\Query\ListDistrictsQuery;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\PagedResult;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\DistrictService
 */
class DistrictServiceListTest extends TestCase
{
    /**
     * @var DistrictService
     */
    private $districtService;

    /**
     * @var DistrictRepository|Stub
     */
    private $districtRepository;

    /**
     * @var CityRepository|Stub
     */
    private $cityRepository;

    protected function setUp(): void
    {
        $this->districtRepository = $this->createStub(DistrictRepository::class);

        $this->cityRepository = $this->createStub(CityRepository::class);

        $this->districtService = new DistrictService(
            $this->districtRepository,
            $this->cityRepository
        );
    }

    public function testListStructure(): void
    {
        $result = $this->createStub(PagedResult::class);
        $this->districtRepository
            ->method("list")
            ->willReturn($result);

        $list = $this->districtService->list($this->createStub(ListDistrictsQuery::class));

        $this->assertSame($result, $list);
    }
}
