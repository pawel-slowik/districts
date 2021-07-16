<?php

declare(strict_types=1);

namespace Districts\DomainModel;

use Districts\DomainModel\Entity\City;

interface CityRepository
{
    public function get(int $id): City;

    public function getByDistrictId(int $districtId): City;

    public function findByName(string $name): ?City;

    public function list(): array;

    public function add(City $city): void;

    public function update(City $city): void;
}
