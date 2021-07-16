<?php

declare(strict_types=1);

namespace Districts\Test\DomainModel\Entity;

use Districts\DomainModel\Entity\City;
use Districts\DomainModel\NotFoundException;
use Districts\DomainModel\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Districts\DomainModel\Entity\City
 */
class CityTest extends TestCase
{
    public function testAddThrowsExceptionOnValidationFailure(): void
    {
        $city = $this->createTestCity();
        $this->expectException(ValidationException::class);
        $city->addDistrict("test", -123.4, 5678);
    }

    public function testUpdateThrowsExceptionOnValidationFailure(): void
    {
        $city = $this->createTestCity();
        $this->expectException(ValidationException::class);
        $city->updateDistrict(1, "test", -123.4, 5678);
    }

    public function testUpdateThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = $this->createTestCity();
        $this->expectException(NotFoundException::class);
        $city->updateDistrict(1, "test", 123.4, 5678);
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = $this->createTestCity();
        $this->expectException(NotFoundException::class);
        $city->removeDistrict(1);
    }

    private function createTestCity(): City
    {
        $city = new City("test");
        return $city;
    }
}
