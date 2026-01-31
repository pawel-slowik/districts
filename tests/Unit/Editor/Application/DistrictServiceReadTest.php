<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Core\Domain\CityRepository;
use Districts\Core\Domain\District;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Application\Query\GetDistrictQuery;
use Districts\Editor\Application\Query\ListDistrictsQuery;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictService::class)]
final class DistrictServiceReadTest extends TestCase
{
    private DistrictService $districtService;

    private DistrictRepository&Stub $districtRepository;

    private DistrictValidator&Stub $districtValidator;

    private CityRepository&Stub $cityRepository;

    protected function setUp(): void
    {
        $this->districtRepository = $this->createStub(DistrictRepository::class);
        $this->districtValidator = $this->createStub(DistrictValidator::class);
        $this->cityRepository = $this->createStub(CityRepository::class);

        $this->districtService = new DistrictService(
            $this->districtValidator,
            $this->districtRepository,
            $this->cityRepository,
        );
    }

    public function testGet(): void
    {
        $query = new GetDistrictQuery(id: 111);

        $repositoryDistrict = $this->createStub(District::class);
        $this->districtRepository
            ->method("get")
            ->willReturnMap([[111, $repositoryDistrict]]);

        $serviceDistrict = $this->districtService->get($query);

        $this->assertSame($repositoryDistrict, $serviceDistrict);
    }

    public function testList(): void
    {
        $result = $this->createStub(PaginatedResult::class);
        $this->districtRepository
            ->method("listWithPagination")
            ->willReturn($result);

        $query = new ListDistrictsQuery(
            ordering: $this->createStub(DistrictOrdering::class),
            filter: $this->createStub(Filter::class),
            pagination: $this->createStub(Pagination::class),
        );

        $list = $this->districtService->list($query);

        $this->assertSame($result, $list);
    }
}
