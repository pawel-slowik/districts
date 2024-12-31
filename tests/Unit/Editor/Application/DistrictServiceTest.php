<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Application\Query\GetDistrictQuery;
use Districts\Editor\Application\Query\ListDistrictsQuery;
use Districts\Editor\Application\ValidationResult;
use Districts\Editor\Domain\Area;
use Districts\Editor\Domain\City;
use Districts\Editor\Domain\CityRepository;
use Districts\Editor\Domain\District;
use Districts\Editor\Domain\DistrictFilter\Filter;
use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictRepository;
use Districts\Editor\Domain\Name;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\Domain\Population;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Editor\Application\DistrictService
 */
class DistrictServiceTest extends TestCase
{
    private DistrictService $districtService;

    /** @var DistrictRepository&Stub */
    private DistrictRepository $districtRepository;

    /** @var DistrictValidator&Stub */
    private DistrictValidator $districtValidator;

    /** @var CityRepository&MockObject */
    private CityRepository $cityRepository;

    protected function setUp(): void
    {
        $this->districtRepository = $this->createStub(DistrictRepository::class);

        $this->districtValidator = $this->createStub(DistrictValidator::class);

        $this->cityRepository = $this->createMock(CityRepository::class);

        $this->districtService = new DistrictService(
            $this->districtValidator,
            $this->districtRepository,
            $this->cityRepository
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
            ->method("list")
            ->willReturn($result);

        $query = new ListDistrictsQuery(
            ordering: $this->createStub(DistrictOrdering::class),
            // phpcs:ignore Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
            filter: (new readonly class () extends Filter {}),
            pagination: new Pagination(1, 1),
        );

        $list = $this->districtService->list($query);

        $this->assertSame($result, $list);
    }

    public function testRemoveConfirmed(): void
    {
        $command = new RemoveDistrictCommand(id: 222);

        $city = $this->createMock(City::class);
        $district = $this->createStub(District::class);
        $district
            ->method("getCity")
            ->willReturn($city);
        $district
            ->method("getName")
            ->willReturn(new Name("name to be removed"));
        $this->districtRepository
            ->method("get")
            ->willReturnMap([[222, $district]]);

        $city
            ->expects($this->once())
            ->method("removeDistrict")
            ->with($this->objectEquals(new Name("name to be removed")));

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->identicalTo($city));

        $this->districtService->remove($command);
    }

    public function testAdd(): void
    {
        $command = new AddDistrictCommand(
            cityId: 333,
            name: "Lorem ipsum",
            area: 12.3,
            population: 456,
        );

        $this->districtValidator
            ->method("validateAdd")
            ->willReturn($this->createValidationSuccessStub());

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("get")
            ->willReturnMap([[333, $city]]);

        $city
            ->expects($this->once())
            ->method("addDistrict")
            ->with(
                $this->objectEquals(new Name("Lorem ipsum")),
                $this->objectEquals(new Area(12.3)),
                $this->objectEquals(new Population(456))
            );

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->identicalTo($city));

        $this->districtService->add($command);
    }

    public function testAddInvalid(): void
    {
        $command = new AddDistrictCommand(cityId: 1, name: "", area: 1, population: 1);

        $this->districtValidator
            ->method("validateAdd")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->add($command);
    }

    public function testAddExceptionErrors(): void
    {
        $command = new AddDistrictCommand(cityId: 1, name: "", area: 1, population: 1);

        $this->districtValidator
            ->method("validateAdd")
            ->willReturn($this->createValidationErrorStub());

        try {
            $this->districtService->add($command);
        } catch (ValidationException $exception) {
            $this->assertContains("foo", $exception->getErrors());
        }
    }

    public function testUpdate(): void
    {
        $command = new UpdateDistrictCommand(
            id: 4,
            name: "update test",
            area: 111.22,
            population: 333,
        );

        $this->districtValidator
            ->method("validateUpdate")
            ->willReturn($this->createValidationSuccessStub());

        $city = $this->createMock(City::class);
        $district = $this->createStub(District::class);
        $district
            ->method("getCity")
            ->willReturn($city);
        $district
            ->method("getName")
            ->willReturn(new Name("previous name"));
        $this->districtRepository
            ->method("get")
            ->willReturnMap([[4, $district]]);

        $city
            ->expects($this->once())
            ->method("updateDistrict")
            ->with(
                $this->objectEquals(new Name("previous name")),
                $this->objectEquals(new Name("update test")),
                $this->objectEquals(new Area(111.22)),
                $this->objectEquals(new Population(333))
            );

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->identicalTo($city));

        $this->districtService->update($command);
    }

    public function testUpdateInvalid(): void
    {
        $command = new UpdateDistrictCommand(id: 1, name: "", area: 1, population: 1);

        $this->districtValidator
            ->method("validateUpdate")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->update($command);
    }

    public function testUpdateExceptionErrors(): void
    {
        $command = new UpdateDistrictCommand(id: 1, name: "", area: 1, population: 1);

        $this->districtValidator
            ->method("validateUpdate")
            ->willReturn($this->createValidationErrorStub());

        try {
            $this->districtService->update($command);
        } catch (ValidationException $exception) {
            $this->assertContains("foo", $exception->getErrors());
        }
    }

    private function createValidationSuccessStub(): ValidationResult
    {
        $validationResult = $this->createStub(ValidationResult::class);
        $validationResult
            ->method("isOk")
            ->willReturn(true);

        return $validationResult;
    }

    private function createValidationErrorStub(): ValidationResult
    {
        $validationResult = $this->createStub(ValidationResult::class);
        $validationResult
            ->method("isOk")
            ->willReturn(false);
        $validationResult
            ->method("getErrors")
            ->willReturn(["foo"]);

        return $validationResult;
    }
}
