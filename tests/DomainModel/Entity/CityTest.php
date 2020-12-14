<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Entity;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\Entity\District;
use Districts\Validator\DistrictValidator;
use Districts\Validator\ValidationResult;
use Districts\Service\NotFoundException;
use Districts\Service\ValidationException;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Districts\DomainModel\Entity\City
 */
class CityTest extends TestCase
{
    public function testSuccessfullAdd(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createPassingValidatorMock();

        $district = $city->addDistrict($districtValidator, "test", 123.4, 5678);
        $this->assertInstanceOf(District::class, $district);
        $this->assertCount(1, $city->listDistricts());
        $this->assertSame("test", $city->listDistricts()[0]->getName());
        $this->assertSame(123.4, $city->listDistricts()[0]->getArea());
        $this->assertSame(5678, $city->listDistricts()[0]->getPopulation());
    }

    public function testAddThrowsExceptionOnValidationFailure(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createFailingValidatorMock();

        $this->expectException(ValidationException::class);
        $city->addDistrict($districtValidator, "test", 123.4, 5678);
    }

    public function testAddDoesNotAppendToDistrictsOnFailure(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createFailingValidatorMock();

        try {
            $city->addDistrict($districtValidator, "test", 123.4, 5678);
        } catch (ValidationException $exception) {
            // noop
        }
        $this->assertCount(0, $city->listDistricts());
    }

    public function testUpdateThrowsExceptionOnValidationFailure(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createFailingValidatorMock();

        $this->expectException(ValidationException::class);
        $city->updateDistrict($districtValidator, 1, "test", 123.4, 5678);
    }

    public function testUpdateThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createPassingValidatorMock();

        $this->expectException(NotFoundException::class);
        $city->updateDistrict($districtValidator, 1, "test", 123.4, 5678);
    }

    public function testSuccessfullUpdate(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createPassingValidatorMock();
        $district = $city->addDistrict($districtValidator, "test", 123.4, 5678);

        // HACK - the id is managed by Doctrine
        $reflection = new \ReflectionClass($district);
        $property = $reflection->getProperty("id");
        $property->setAccessible(true);
        $property->setValue($district, 123456789);

        $city->updateDistrict($districtValidator, 123456789, "updated name", 1.2, 34);

        $this->assertCount(1, $city->listDistricts());
        $this->assertSame("updated name", $city->listDistricts()[0]->getName());
        $this->assertSame(1.2, $city->listDistricts()[0]->getArea());
        $this->assertSame(34, $city->listDistricts()[0]->getPopulation());
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createPassingValidatorMock();
        $this->expectException(NotFoundException::class);
        $city->removeDistrict(1);
    }

    public function testSuccessfullRemove(): void
    {
        $city = $this->createTestCity();
        $districtValidator = $this->createPassingValidatorMock();
        $district = $city->addDistrict($districtValidator, "test", 123.4, 5678);

        // HACK - the id is managed by Doctrine
        $reflection = new \ReflectionClass($district);
        $property = $reflection->getProperty("id");
        $property->setAccessible(true);
        $property->setValue($district, 123456789);

        $city->removeDistrict(123456789);
        $this->assertCount(0, $city->listDistricts());
    }

    private function createTestCity(): City
    {
        $city = new City("test");
        return $city;
    }

    private function createPassingValidatorMock(): MockObject
    {
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->method("isOk")->willReturn(true);
        $validator = $this->createMock(DistrictValidator::class);
        $validator->method("validate")->willReturn($validationResult);
        return $validator;
    }

    private function createFailingValidatorMock(): MockObject
    {
        $validationResult = $this->createMock(ValidationResult::class);
        $validationResult->method("isOk")->willReturn(false);
        $validator = $this->createMock(DistrictValidator::class);
        $validator->method("validate")->willReturn($validationResult);
        return $validator;
    }
}
