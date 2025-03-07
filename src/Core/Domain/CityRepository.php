<?php

declare(strict_types=1);

namespace Districts\Core\Domain;

interface CityRepository
{
    public function get(int $id): City;

    public function findByName(string $name): ?City;

    /**
     * @return City[]
     */
    public function list(): array;

    public function add(City $city): void;

    public function update(City $city): void;
}
