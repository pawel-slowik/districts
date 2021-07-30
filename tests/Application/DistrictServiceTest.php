<?php

declare(strict_types=1);

namespace Districts\Test\Application;

use Districts\Application\Command\AddDistrictCommand;
use Districts\Application\Command\RemoveDistrictCommand;
use Districts\Application\Command\UpdateDistrictCommand;
use Districts\Application\CommandException;
use Districts\Application\DistrictService;
use Districts\Application\NotFoundException;
use Districts\Application\Query\GetDistrictQuery;
use Districts\Application\ValidationException as RequestValidationException;
use Districts\DomainModel\CityRepository;
use Districts\DomainModel\DistrictRepository;
use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
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
     * @var CityRepository|MockObject
     */
    private $cityRepository;

    protected function setUp(): void
    {
        $this->districtRepository = $this->createStub(DistrictRepository::class);

        $this->cityRepository = $this->createMock(CityRepository::class);

        $this->districtService = new DistrictService(
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
            ->with($this->equalTo(111))
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

    public function testRemoveConfirmed(): void
    {
        $command = $this->createStub(RemoveDistrictCommand::class);
        $command->method("getId")->willReturn(222);
        $command->method("isConfirmed")->willReturn(true);

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("getByDistrictId")
            ->with($this->equalTo(222))
            ->willReturn($city);

        $city
            ->expects($this->once())
            ->method("removeDistrict")
            ->with($this->equalTo(222));

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->equalTo($city));

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

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("get")
            ->with($this->equalTo(333))
            ->willReturn($city);

        $city
            ->expects($this->once())
            ->method("addDistrict")
            ->with(
                $this->equalTo("Lorem ipsum"),
                $this->equalTo(12.3),
                $this->equalTo(456)
            );

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->equalTo($city));

        $this->districtService->add($command);
    }

    public function testAddInvalid(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);

        $this->cityRepository
            ->method("get")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(RequestValidationException::class);

        $this->districtService->add($command);
    }

    public function testUpdate(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);
        $command->method("getId")->willReturn(4);
        $command->method("getName")->willReturn("update test");
        $command->method("getArea")->willReturn(111.22);
        $command->method("getPopulation")->willReturn(333);

        $city = $this->createMock(City::class);

        $this->cityRepository
            ->method("getByDistrictId")
            ->with($this->equalTo(4))
            ->willReturn($city);

        $city
            ->expects($this->once())
            ->method("updateDistrict")
            ->with(
                $this->equalTo(4),
                $this->equalTo("update test"),
                $this->equalTo(111.22),
                $this->equalTo(333)
            );

        $this->cityRepository
            ->expects($this->once())
            ->method("update")
            ->with($this->equalTo($city));

        $this->districtService->update($command);
    }

    public function testUpdateNonExistent(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->cityRepository
            ->method("getByDistrictId")
            ->will($this->throwException(new NotFoundInRepositoryException()));

        $this->cityRepository
            ->expects($this->never())
            ->method("update");

        $this->expectException(NotFoundException::class);

        $this->districtService->update($command);
    }
}
