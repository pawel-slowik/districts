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
        $city = new City("test");

        $this->expectException(ValidationException::class);

        $city->addDistrict("test", -123.4, 5678);
    }

    public function testUpdateThrowsExceptionOnValidationFailure(): void
    {
        $city = new City("test");

        $this->expectException(ValidationException::class);

        $city->updateDistrict(1, "test", -123.4, 5678);
    }

    public function testUpdateThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = new City("test");

        $this->expectException(NotFoundException::class);

        $city->updateDistrict(1, "test", 123.4, 5678);
    }

    public function testRemoveThrowsExceptionOnUnknownDistrictId(): void
    {
        $city = new City("test");

        $this->expectException(NotFoundException::class);

        $city->removeDistrict(1);
    }
}
