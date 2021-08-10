<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\DistrictService;
use Districts\Application\DistrictValidator;
use Districts\Application\Exception\CommandException;
use Districts\Application\Exception\NotFoundException;
use Districts\Application\Exception\ValidationException;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\Query\ListDistrictsQuery;
use Districts\Application\ValidationResult;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\DomainModel\PagedResult;
use Districts\DomainModel\VO\Area;
use Districts\DomainModel\VO\Name;
use Districts\DomainModel\VO\Population;
use Districts\Infrastructure\NotFoundInRepositoryException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\Application\DistrictService
 */
class DistrictServiceTest extends TestCase
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
     * @var DistrictValidator|Stub
     */
    private $districtValidator;

    /**
     * @var CityRepository|MockObject
     */
    private $cityRepository;

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
        $query = $this->createStub(GetDistrictQuery::class);
        $query->method("getId")->willReturn(111);

        $repositoryDistrict = $this->createStub(District::class);
        $this->districtRepository
            ->method("get")
            ->with($this->identicalTo(111))
            ->willReturn($repositoryDistrict);

        $serviceDistrict = $this->districtService->get($query);

        $this->assertSame($repositoryDistrict, $serviceDistrict);
    }

    public function testGetNonExistent(): void
    {
        $this->districtRepository
            ->method("get")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->expectException(NotFoundException::class);

        $this->districtService->get($this->createStub(GetDistrictQuery::class));
    }

    public function testList(): void
    {
        $result = $this->createStub(PagedResult::class);
        $this->districtRepository
            ->method("list")
            ->willReturn($result);

        $list = $this->districtService->list($this->createStub(ListDistrictsQuery::class));

        $this->assertSame($result, $list);
    }

    public function testRemoveConfirmed(): void
    {
        $command = $this->createStub(RemoveDistrictCommand::class);
        $command->method("getId")->willReturn(222);
        $command->method("isConfirmed")->willReturn(true);

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("getByDistrictId")
            ->with($this->identicalTo(222))
            ->willReturn($city);

        $city
            ->expects($this->once())
            ->method("removeDistrict")
            ->with($this->identicalTo(222));

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->identicalTo($city));

        $this->districtService->remove($command);
    }

    public function testRemoveUnconfirmed(): void
    {
        $command = $this->createStub(RemoveDistrictCommand::class);
        $command->method("isConfirmed")->willReturn(false);

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(CommandException::class);

        $this->districtService->remove($command);
    }

    public function testRemoveNonExistent(): void
    {
        $command = $this->createStub(RemoveDistrictCommand::class);
        $command->method("isConfirmed")->willReturn(true);

        $this->cityRepository
            ->method("getByDistrictId")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(NotFoundException::class);

        $this->districtService->remove($command);
    }

    public function testAdd(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);
        $command->method("getCityId")->willReturn(333);
        $command->method("getName")->willReturn("Lorem ipsum");
        $command->method("getArea")->willReturn(12.3);
        $command->method("getPopulation")->willReturn(456);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationSuccessStub());

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("get")
            ->with($this->identicalTo(333))
            ->willReturn($city);

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

    public function testAddForNonExistentCity(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationSuccessStub());

        $this->cityRepository
            ->method("get")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->add($command);
    }

    public function testAddInvalid(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->add($command);
    }

    public function testAddExceptionErrors(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationErrorStub());

        try {
            $this->districtService->add($command);
        } catch (ValidationException $exception) {
            $this->assertContains("foo", $exception->getErrors());
        }
    }

    public function testUpdate(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);
        $command->method("getId")->willReturn(4);
        $command->method("getName")->willReturn("update test");
        $command->method("getArea")->willReturn(111.22);
        $command->method("getPopulation")->willReturn(333);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationSuccessStub());

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("getByDistrictId")
            ->with($this->identicalTo(4))
            ->willReturn($city);

        $city
            ->expects($this->once())
            ->method("updateDistrict")
            ->with(
                $this->identicalTo(4),
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

    public function testUpdateNonExistent(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationSuccessStub());

        $this->cityRepository
            ->method("getByDistrictId")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(NotFoundException::class);

        $this->districtService->update($command);
    }

    public function testUpdateInvalid(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
            ->willReturn($this->createValidationErrorStub());

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(ValidationException::class);

        $this->districtService->update($command);
    }

    public function testUpdateExceptionErrors(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->districtValidator
            ->method("validate")
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
