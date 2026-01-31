<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Core\Domain\Area;
use Districts\Core\Domain\City;
use Districts\Core\Domain\CityRepository;
use Districts\Core\Domain\District;
use Districts\Core\Domain\Name;
use Districts\Core\Domain\Population;
use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\RemoveDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Application\ValidationResult;
use Districts\Editor\Domain\DistrictRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictService::class)]
final class DistrictServiceWriteTest extends TestCase
{
    private DistrictService $districtService;

    private DistrictRepository&Stub $districtRepository;

    private DistrictValidator&Stub $districtValidator;

    private CityRepository&MockObject $cityRepository;

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
        $command = $this->createStub(AddDistrictCommand::class);

        $this->districtValidator
            ->method("validateAdd")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->add($command);
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
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->districtValidator
            ->method("validateUpdate")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->update($command);
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

        return $validationResult;
    }
}
